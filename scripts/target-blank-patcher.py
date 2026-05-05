"""Helper logic for patching <a> tags with target/rel for external links.

Used as a module from the orchestrator that calls the WP MCP. We only ship
the pure patching logic here; the per-post fetch/update is driven from the
agent loop because tool calls live there.
"""

import re

INTERNAL_PREFIXES = (
    '/',
    '#',
    'mailto:',
    'tel:',
    'https://eeiblog.com',
    'http://eeiblog.com',
    'https://www.eeiblog.com',
    'http://www.eeiblog.com',
)

A_TAG_RE = re.compile(r'<a\b[^>]*>', re.IGNORECASE)
HREF_RE = re.compile(r'''href\s*=\s*["']([^"']*)["']''', re.IGNORECASE)
TARGET_RE = re.compile(r'''target\s*=\s*["']_blank["']''', re.IGNORECASE)
REL_RE = re.compile(r'''rel\s*=\s*["']([^"']*)["']''', re.IGNORECASE)


def is_internal(href: str) -> bool:
    h = href.strip()
    if not h:
        # Empty href – treat as internal/no-op (don't touch)
        return True
    return any(h.startswith(p) for p in INTERNAL_PREFIXES)


def patch_a_tag(tag: str) -> tuple[str, bool]:
    """Return (new_tag, modified)."""
    href_m = HREF_RE.search(tag)
    if not href_m:
        return tag, False
    href = href_m.group(1)
    if is_internal(href):
        return tag, False

    new_tag = tag
    modified = False

    # 1. rel handling — always ensure it has noopener noreferrer.
    rel_m = REL_RE.search(new_tag)
    if rel_m:
        existing_tokens = rel_m.group(1).split()
        token_set = {t for t in existing_tokens if t}
        needed = {'noopener', 'noreferrer'}
        if not needed.issubset(token_set):
            # Preserve original order, append missing tokens.
            for t in needed:
                if t not in token_set:
                    existing_tokens.append(t)
                    token_set.add(t)
            new_rel_value = ' '.join(existing_tokens)
            new_tag = new_tag[:rel_m.start()] + f'rel="{new_rel_value}"' + new_tag[rel_m.end():]
            modified = True
    else:
        # Insert rel before the closing >
        # Find end (handles self-closing? <a> tags aren't self-closing in HTML, ignore />)
        if new_tag.endswith('>'):
            new_tag = new_tag[:-1].rstrip() + ' rel="noopener noreferrer">'
            modified = True

    # 2. target handling
    if not TARGET_RE.search(new_tag):
        if new_tag.endswith('>'):
            new_tag = new_tag[:-1].rstrip() + ' target="_blank">'
            modified = True

    return new_tag, modified


def patch_content(html: str) -> tuple[str, int]:
    """Patch all <a> open tags in html. Returns (new_html, count_modified)."""
    count = 0

    def _sub(m):
        nonlocal count
        new_tag, modified = patch_a_tag(m.group(0))
        if modified:
            count += 1
        return new_tag

    return A_TAG_RE.sub(_sub, html), count


def quick_self_test():
    cases = [
        # external, no rel, no target
        ('<a href="https://halleonard.com/x">x</a>',
         '<a href="https://halleonard.com/x" rel="noopener noreferrer" target="_blank">x</a>',
         1),
        # external, has nofollow rel
        ('<a href="https://x.com" rel="nofollow">x</a>',
         '<a href="https://x.com" rel="nofollow noopener noreferrer" target="_blank">x</a>',
         1),
        # external, already has target
        ('<a href="https://x.com" target="_blank" rel="noopener noreferrer">x</a>',
         '<a href="https://x.com" target="_blank" rel="noopener noreferrer">x</a>',
         0),
        # external with target but no rel
        ('<a href="https://x.com" target="_blank">x</a>',
         '<a href="https://x.com" target="_blank" rel="noopener noreferrer">x</a>',
         1),
        # internal absolute eeiblog.com
        ('<a href="https://eeiblog.com/foo">foo</a>',
         '<a href="https://eeiblog.com/foo">foo</a>',
         0),
        # internal www
        ('<a href="https://www.eeiblog.com/bar">bar</a>',
         '<a href="https://www.eeiblog.com/bar">bar</a>',
         0),
        # relative
        ('<a href="/foo">foo</a>', '<a href="/foo">foo</a>', 0),
        # mailto
        ('<a href="mailto:x@y.com">m</a>', '<a href="mailto:x@y.com">m</a>', 0),
        # tel
        ('<a href="tel:+1234">t</a>', '<a href="tel:+1234">t</a>', 0),
        # anchor
        ('<a href="#sec">a</a>', '<a href="#sec">a</a>', 0),
        # external with extra attributes
        ('<a class="btn" href="https://halleonard.com/x" data-id="3">x</a>',
         '<a class="btn" href="https://halleonard.com/x" data-id="3" rel="noopener noreferrer" target="_blank">x</a>',
         1),
        # external with rel already including noopener but missing noreferrer
        ('<a href="https://x.com" rel="noopener">x</a>',
         '<a href="https://x.com" rel="noopener noreferrer" target="_blank">x</a>',
         1),
        # external with both noopener and noreferrer but no target
        ('<a href="https://x.com" rel="noopener noreferrer">x</a>',
         '<a href="https://x.com" rel="noopener noreferrer" target="_blank">x</a>',
         1),
    ]
    fails = []
    for src, want, want_count in cases:
        got, count = patch_content(src)
        if got != want or count != want_count:
            fails.append((src, want, got, want_count, count))
    return fails


if __name__ == '__main__':
    fails = quick_self_test()
    if fails:
        for f in fails:
            print('FAIL:', f)
    else:
        print('all tests passed')
