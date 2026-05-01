# План миграции и SEO-оптимизации Essential Elements Interactive

**Источник:** eeiblog.com (Squarespace)
**Цель:** rkislenok-xwlsc.wpcomstaging.com (WordPress.com Atomic, тема `eeiblog-theme`)
**Дата составления:** 30 апреля 2026
**Статус:** черновик — требует утверждения Романа

---

## 1. Резюме аудита

### 1.1 Что уже сделано

- 119 страниц перенесено в WordPress, у большинства — **чистые slug'и без дат** (`/soundcheck/`, `/eei-overview-1/`, `/getting-started/` и т.д.).
- Кастомная тема `eeiblog-theme` установлена и хранится в локальном git-репо (`wordpress-theme/eeiblog-theme/`).
- 3 поста, относящихся к старым материалам (Create a Student Account, Creating a Rubric in EEi, Getting Your Students Enrolled), — попали в WP как `post`, не `page`.
- 1 дефолтный пост `Hello World!`.

### 1.2 Что не сделано

- **SEO-метаданные пустые на ВСЕХ 119 страницах** (jetpack_seo_html_title, advanced_seo_description, OG — везде null).
- Сайт в режиме **Coming Soon** (`is_site_launched: false`) — не индексируется.
- В настройках сайта пустой `blogdescription` (tagline).
- Есть **дубликаты, рабочие копии и junk-страницы** (см. раздел 4).
- **Посты** имеют URL вида `/2018/09/21/2018-9-21-create-a-student-account/` — некрасивые и некороткие slug'и, плюс date-based permalink.
- Категории и теги не структурированы — все 4 поста в `Uncategorized`.
- Нет sitemap.xml / robots.txt / Schema.org (нужно проверить, что Jetpack/Yoast делают).

### 1.3 Ограничения MCP (важно!)

Текущий конфиг WordPress MCP-коннектора отключает критически важные операции для миграции:

| Операция | Статус | Что блокирует |
|---|---|---|
| `pages.create` | ❌ disabled | Создание новых страниц |
| `pages.update` | ❌ disabled | **Изменение slug, title, content, meta** существующих страниц |
| `pages.delete` | ❌ disabled | Удаление дубликатов и junk-страниц |
| `posts.delete` | ❌ disabled | Удаление Hello World и старых постов |
| `media.delete` | ❌ disabled | Чистка медиа-библиотеки |
| `categories.delete` / `tags.delete` | ❌ disabled | Чистка таксономий |

✅ Доступно: `posts.create/update`, `media.update` (alt_text/caption), `categories.create/update`, `tags.create/update`, `settings.update`, `manage-site.launch`.

**Решение:** перед началом работы Роману нужно включить отключённые операции в настройках MCP-коннектора WordPress.com на стороне Cowork. Без этого все правки страниц придётся делать вручную через WP-админку.

---

## 2. Карта контента: WP ↔ eeiblog.com

### 2.1 Группа "Static / Hub Pages" (главные страницы продукта)

| WP slug | Заголовок | Оригинал на eeiblog.com | Действие |
|---|---|---|---|
| `eei-overview-1` | EEi Overview | `/eei-overview-1` | ✅ Сохранить как канонический |
| `eei-overview` | EE Overview (New) | — | ⚠️ Сравнить с -1 → объединить → 301 |
| `eei-overview-1-1` | EEi Overview (working copy CB) | — | ❌ Удалить (рабочая копия) |
| `soundcheck` | SoundCheck | `/soundcheck` | ✅ |
| `getting-started` | Five Ways to Get Started | `/getting-started` | ✅ |
| `eei-song-updates` | EEi Song Updates | `/eei-song-updates` | ✅ |
| `teacher-audio-feedback` | Teacher Audio Feedback | `/teacher-audio-feedback` | ✅ |
| `eei-webinars` | EEi Webinars | `/eei-webinars` | ✅ |
| `eei-google-classroom-integration` | EEi Google Classroom Integration | `/eei-google-classroom-integration` | ✅ |
| `correlatedcollections` | EE Correlated Collections | `/correlatedcollections` | ✅ |
| `ee-digital-books` | EE Digital Books | `/ee-digital-books` | ✅ |
| `subscribe` | Subscribe | `/subscribe` | ✅ |
| `eei-tutorials` | EEi Tutorials | — | ✅ |
| `teachers` | Teachers | (часть Home) | ✅ |
| `students` | Students | (часть Home) | ✅ |
| `eei-lessons` | Teaching Tips | `/eei-lessons` | ✅ Хаб блога |
| `news` | EEi News | — | ✅ |

### 2.2 Группа "Method Books" (книги — лендинги)

| WP slug | Заголовок | Действие |
|---|---|---|
| `ee-books-overview` | EE Books Overview | ✅ |
| `ee-band-method` | EE Band Method | ✅ |
| `ee-strings-method` | EE Strings Method | ✅ |
| `eebandbook1` / `eebandbook2` / `eebandbook3` | EE Band Book 1/2/3 | ✅ |
| `eestringsbook1` / `eestringsbook2` / `eestringsbook3` | EE Strings Book 1/2/3 | ✅ |
| `ee-method-books-lp-cb` | EE Method Books LP (CB) | ⚠️ Сравнить с `ee-books-overview` |
| `ee-method-books-lp-cb-copy` | EE Method Books LP (CB) (Side By Side) | ❌ Удалить (копия) |

### 2.3 Группа "Lead-gen Forms / Download Pages" (формы скачивания книг)

Каждая книга имеет пару `*request` (форма) + `*download` (страница после отправки):
- `eebandbook1request` / `eebandbook1download`
- `eebandbook2request` / `eebandbook2download`
- `eebandbook3request` / `eebandbook3download`
- `eestringsbook1request` / `eestringsbook1download`
- `eestringsbook2request` / `eestringsbook2download`
- `eestringsbook3request` / `eestringsbook3download`

→ ✅ Оставить, но **noindex** (это thank-you / lead-gen страницы — индексировать не надо).

### 2.4 Группа "Teaching Tips / Blog Articles" (контент-статьи — потенциальные блог-посты)

Эти страницы по смыслу — статьи, а не страницы. Перечислены на хабе `/eei-lessons`:

| WP slug | Заголовок |
|---|---|
| `establishing-an-effective-rehearsal-routine` | Establishing an Effective and Positive Rehearsal Routine |
| `chair-placement-audition-tips` | Chair Placement Audition Tips |
| `backtoschoolpercussiontips` | Back-to-School Percussion Tips for Band Directors |
| `settingthetone` | Setting the Tone for a Successful School Year |
| `easy-strategy-rhythm-of-the-day` | Easy Strategy: Rhythm of the Day |
| `classroom-management` | Classroom Management |
| `fromthepodiumtothepublisher` | Finding Methods that Support Your Teaching |
| `band-instrument-fundamentals2` | Band Instrument Fundamentals |
| `eei-webinar-starting-strings` | EEi Webinar Starting Strings |
| `on-demand-starting-band` | On-Demand Starting Band |
| `in-class-assessment` | EEi In-Class Grading & Recording |
| `eei-video-assignments` | EEi Video Assignments |
| `submit-recording-tutorial` | Submit Recording Tutorial |
| `eei-webinar-engaging-students` | EEi Webinar — Engaging Students |
| `summer-practice` | Summer Practice Ideas |
| `music-theory-intro` | EEi Introduction to Music Theory |
| `rhythm-writing-system` | Rhythm Writing System |
| `eei-webinar-kids-on-track` | EEi Webinar Kids on Track |
| `brass-mouhtpiece` | Brass Mouthpiece |
| `string-bass-tip` | String Bass Tip |
| `rhythm-assignment` | Rhythm Assignment |
| `holiday-practice-tips` | Holiday Practice Tips |
| `recordingassignments` | Audio & Video Recording Assignments in EEi |

→ **Решение нужно от Романа:** оставлять как `page` (как есть в Squarespace) или конвертировать в `post` с категорией `Teaching Tips`? Конверт в посты даст: правильную хронологию, RSS, категории/теги, archive-страницы. Но потребует переноса в админке.

### 2.5 Группа "Account / Tutorial / Tech Support"

`create-a-teacher-account`, `create-an-eei-student-account`, `school-account2`, `individual-account2`, `eei-school-code`, `school-account-id-schoolcode`, `school-eei-account-school-code-only`, `student-invite-link`, `submit-audio-recording`, `submit-video-assignment`, `eei-video-assignments-s`, `eeischoolsetup`, `revisitingeei`, `recording-assignments`, `rubrics`, `music-studio`, `help-topic-suggestion`, `feedback`

→ ✅ Оставить.

### 2.6 Группа "Old / Legacy / Test / Junk"

| WP slug | Заголовок | Действие |
|---|---|---|
| `student-account-old` | Student Account | ❌ Удалить, 301 → `create-an-eei-student-account` |
| `eei-lessons-old` | EEi Lessons (старая версия) | ❌ Удалить, 301 → `eei-lessons` |
| `eei-lessons-2` | EEi Lessons 2 (черновик) | ❌ Удалить |
| `eei-webinars2` | EEi Webinars2 (запись прошедшего вебинара) | ⚠️ Архивировать или объединить с `eei-webinars` |
| `eei-webinar-starting-band` / `eei-webinar-starting-band-repeat` | дубликаты | ⚠️ Объединить |
| `new-page-1` | "See Inside EEi" (с lorem ipsum) | ❌ Удалить |
| `spacer` | "Space" (заглушка) | ❌ Удалить |
| `sbo` | EEiBlogSBO (пусто) | ❌ Удалить |
| `asta` | asta (пусто) | ❌ Удалить |
| `instrumentalist` | Instrumentalist (пусто) | ❌ Удалить |
| `hybrid` / `ee-hybrid-method` | EE Hybrid (только "Questions?") | ❌ Удалить или дозаполнить |
| `southwestgiveaway25` | Southwest Summer Music Exhibition 2025 | ⚠️ Архивировать (giveaway завершён) |
| `musiciseessential` | #MusicIsEEssential 2025 | ⚠️ Архивировать |
| `hl-midwest-spree` | HL Midwest Clinic Shopping Spree | ⚠️ Архивировать |
| `eeidistancelearning` / `eeidistancelearning2` | Distance Learning (COVID-эпоха) | ⚠️ Объединить или архивировать |
| `end-of-year` | End of Year (COVID-эпоха) | ⚠️ Архивировать |
| `tim` / `dr-tim` | Dr. Tim формы | ⚠️ Объединить |

### 2.7 Группа "Posts" (4 шт. — все требуют решения)

| ID | Slug | Title | Действие |
|---|---|---|---|
| 3 | `hello-world` | Hello World! | ❌ Удалить (дефолт WP) |
| 11 | `2018-9-21-create-a-student-account-eymyf` | Getting Your Students Enrolled (draft) | ⚠️ Объединить с page `create-an-eei-student-account` или опубликовать с чистым slug `getting-your-students-enrolled` |
| 4 | `2018-9-21-create-a-student-account` | Create a Student Account | ⚠️ Дубль page-материала. Удалить, 301 на `create-an-eei-student-account` |
| 8 | `2018-9-20-creating-a-rubric-in-eei` | Creating a Rubric in EEi | ⚠️ Объединить с page `rubrics` ИЛИ переименовать slug в `creating-a-rubric-in-eei` |

---

## 3. План действий: 5 фаз

### Фаза 0: Подготовка (требуется Роман)

1. **Включить в MCP-настройках операции:** `pages.create`, `pages.update`, `pages.delete`, `posts.delete`, `media.delete`, `categories.delete`, `tags.delete`. Без них основные правки невозможны через MCP.
2. **Поделиться правилами для текстов** для образовательных сайтов (упомянуты в ответе) — буду использовать как шаблон при оптимизации заголовков, описаний и контент-апдейтов.
3. **Подтвердить ключевые решения:**
   - конвертировать ли Teaching Tips статьи (раздел 2.4) из `page` в `post`?
   - какие из "Old / Legacy / Junk" страниц действительно удалить, а какие архивировать?
   - менять ли permalink-структуру для постов с `/%year%/%monthnum%/%day%/%postname%/` на `/%postname%/` (или `/blog/%postname%/`)?
   - запускать ли сайт (отключать Coming Soon) сейчас или после миграции?

### Фаза 1: Уборка (cleanup) — 1-2 часа

- Удалить junk-страницы (раздел 2.6, Action ❌).
- Удалить `Hello World!` пост.
- Объединить дубликаты `eei-overview` ↔ `eei-overview-1` ↔ `eei-overview-1-1` → один канонический.
- Удалить рабочую копию `ee-method-books-lp-cb-copy`.
- Перенести 3 EEi-поста в страницы или дать им чистые slug'и (плюс 301 со старых URL).
- Настроить permalink-структуру (если решим менять).

### Фаза 2: Технический SEO — 2-3 часа

- **Permalink-структура**: переключить с date-based на `/%postname%/` или `/blog/%postname%/`.
- **301-редиректы** со старых slug'ов (для всех удалённых/переименованных страниц).
- **Sitemap.xml**: проверить, что Jetpack генерирует корректно (стандартно: `/sitemap.xml`).
- **Robots.txt**: проверить, что не блокирует индексацию после launch.
- **Schema.org разметка**:
  - `Organization` schema на всём сайте (Hal Leonard / EEi).
  - `Article` schema на статьях Teaching Tips.
  - `BreadcrumbList` на всех страницах.
  - `VideoObject` для страниц-вебинаров.
  - `Course` или `EducationalOccupationalProgram` для Method Books (опционально).
- **OpenGraph + Twitter Cards** через Jetpack.
- **noindex** для thank-you страниц (`*download`, `*request`).
- **canonical URLs** проверить корректность.
- **Tagline** сайта: добавить в settings (`blogdescription`) — например, "The cloud-based companion to the Essential Elements method books".
- **Site logo / favicon** проверить.

### Фаза 3: Метаданные на 119 страниц — 4-6 часов

Заполнить для каждой опубликованной страницы:

- `jetpack_seo_html_title` — SEO-title (60-65 символов)
- `advanced_seo_description` — meta description (150-160 символов)
- `featured_media` для OpenGraph image (если ещё не задана)
- `alt_text` на всех изображениях

→ Подготовлю отдельную таблицу `seo-metadata.csv` со всеми 119 строками, дам на ревью, потом массово запишу через `pages.update`.

### Фаза 4: Контент-оптимизация и новый контент — итеративно

- Аудит ключевых слов (см. раздел 5).
- Пройтись по топ-30 приоритетным страницам и оптимизировать H1/H2, плотность ключей, внутренние ссылки, alt-тексты.
- Использовать правила Романа для образовательных текстов как guidelines.
- **Контент-гэпы** — темы, которые имеет смысл добавить (раздел 6).

### Фаза 5: Запуск и контроль

- Запустить сайт (`manage-site.launch`).
- Зарегистрировать в Google Search Console и Bing Webmaster Tools — submit sitemap.
- Настроить базовый трекинг (GA4 / Plausible).
- Через 2-4 недели — отчёт по индексации, позициям, замечаниям.

---

## 4. SEO: ключевые слова и контент-стратегия

### 4.1 Целевая аудитория

- **Primary**: учителя музыки (band/strings) в начальных, средних и старших школах США (K-12).
- **Secondary**: студенты-музыканты, родители, дилеры музыкальных инструментов (Hal Leonard).

### 4.2 Тематические кластеры (предлагаю как стартовые)

| Кластер | Ядро запросов | Страницы-якоря |
|---|---|---|
| **Brand / Product** | essential elements interactive, EEi, Hal Leonard EE | `/`, `/eei-overview-1`, `/ee-books-overview` |
| **Method Books** | essential elements band book, essential elements strings, beginner band method | `/ee-band-method`, `/ee-strings-method`, `/eebandbook1`-`3`, `/eestringsbook1`-`3` |
| **Practice & Assessment** | music practice assessment software, soundcheck performance assessment, music recording assignment | `/soundcheck`, `/recording-assignments`, `/recordingassignments`, `/in-class-assessment` |
| **Tools for Teachers** | music classroom management, beginner band tips, rehearsal routine, chair placement | `/classroom-management`, `/establishing-an-effective-rehearsal-routine`, `/chair-placement-audition-tips`, `/getting-started` |
| **Music Education Pedagogy** | rhythm counting, brass mouthpiece exercises, string bass technique, music theory worksheets | `/rhythm-writing-system`, `/brass-mouhtpiece`, `/string-bass-tip`, `/music-theory-intro` |
| **Tech Integration** | google classroom music, music distance learning, video recording assignment band | `/eei-google-classroom-integration`, `/eei-video-assignments`, `/eeidistancelearning` |
| **Webinars / On-Demand** | beginning band webinar, beginning strings webinar | `/eei-webinars`, `/on-demand-starting-band`, `/eei-webinar-starting-strings` |

### 4.3 Принципы заголовков

- **H1**: 1 на страницу, содержит главный запрос кластера.
- **SEO-title**: бренд в конце — `{Topic} | EEi Blog` или `{Topic} | Essential Elements Interactive`.
- **Meta-description**: 150-160 символов, включает CTA.
- **OG-image**: 1200×630, фирменные цвета, понятный текст.

---

## 5. Контент-гэпы (что хочется добавить)

На основе анализа Teaching Tips и интересов аудитории — кандидаты для новых статей (после согласования с правилами Романа для текстов):

1. **EEi for First-Year Teachers** — guide для дебютантов.
2. **Differentiated Instruction in Mixed-Level Band** — горячая тема в music ed.
3. **Using EEi for Solo & Ensemble Festival Prep** — сезонный контент.
4. **EEi vs. Other Music Practice Apps** — сравнительный обзор (если уместно с т.з. Hal Leonard).
5. **Parent's Guide to EEi** — родительская перспектива.
6. **EEi Glossary / FAQ** — long-tail SEO (вопросы вроде "what is EEi school code", "how to recover EEi student password" и т.п.).
7. **Recording & Assessment Best Practices** — расширение `/recordingassignments`.
8. **Music Educator Self-Care / Burnout Prevention** — soft topic, привлекает аудиторию.
9. **Year-end Awards & Celebration Ideas** — сезонный контент (на замену старой `end-of-year`).
10. **Teaching Music Theory Step-by-Step** — расширение `/music-theory-intro` в серию.

---

## 6. Что нужно от Романа сейчас

1. ✅ **Ответить на вопросы Фазы 0** (см. раздел 3, "Подготовка"): MCP-разрешения, post vs. page для Teaching Tips, permalink, launch timing, что точно удалять.
2. ✅ **Поделиться правилами для образовательных текстов** — приложить файлом или вставить в чат.
3. ✅ **Подтвердить тематические кластеры** (раздел 4.2) или добавить свои.
4. ✅ **Решить, какие из тем-кандидатов** (раздел 5) интересны для разработки.

После этого я начну с Фазы 1 (уборка) и буду показывать результат пакетами по разделам.

---

## Приложения

- `wordpress-inventory.json` — полный inventory 119 страниц + 4 постов (для справки).
- `seo-metadata.csv` — будет создан на Фазе 3 (черновик метаданных).
- `redirects-map.csv` — будет создан на Фазе 1 (старый URL → новый URL).
