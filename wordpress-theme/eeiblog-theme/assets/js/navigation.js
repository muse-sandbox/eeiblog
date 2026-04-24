/**
 * EEi Blog — navigation.js
 * Handles mobile menu toggle and keyboard-accessible dropdowns.
 */
( function () {
    'use strict';

    document.addEventListener( 'DOMContentLoaded', function () {
        var toggle   = document.querySelector( '.menu-toggle' );
        var nav      = document.querySelector( '.main-navigation' );
        var parents  = document.querySelectorAll( '.menu-item-has-children' );

        // ── Mobile hamburger ──────────────────────────────────
        if ( toggle && nav ) {
            toggle.addEventListener( 'click', function () {
                var isOpen = nav.classList.toggle( 'is-open' );
                toggle.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
            } );
        }

        // ── Dropdown parent items (mobile tap + keyboard) ─────
        parents.forEach( function ( parent ) {
            var link = parent.querySelector( 'a' );

            // Keyboard: open on Enter/Space when focus is on parent link
            if ( link ) {
                link.addEventListener( 'keydown', function ( e ) {
                    if ( e.key === 'Enter' || e.key === ' ' ) {
                        var isMobile = window.innerWidth < 768;
                        if ( isMobile ) {
                            e.preventDefault();
                            parent.classList.toggle( 'is-open' );
                            var expanded = parent.classList.contains( 'is-open' );
                            link.setAttribute( 'aria-expanded', expanded ? 'true' : 'false' );
                        }
                    }
                } );
            }

            // Mobile: tap on parent link toggles sub-menu instead of navigating
            if ( link ) {
                link.addEventListener( 'click', function ( e ) {
                    if ( window.innerWidth < 768 ) {
                        e.preventDefault();
                        parent.classList.toggle( 'is-open' );
                        var expanded = parent.classList.contains( 'is-open' );
                        link.setAttribute( 'aria-expanded', expanded ? 'true' : 'false' );
                    }
                } );
            }
        } );

        // ── Close menu on outside click ───────────────────────
        document.addEventListener( 'click', function ( e ) {
            if ( nav && ! nav.contains( e.target ) && toggle && ! toggle.contains( e.target ) ) {
                nav.classList.remove( 'is-open' );
                if ( toggle ) toggle.setAttribute( 'aria-expanded', 'false' );
            }
        } );

        // ── Close on Escape ───────────────────────────────────
        document.addEventListener( 'keyup', function ( e ) {
            if ( e.key === 'Escape' ) {
                if ( nav ) nav.classList.remove( 'is-open' );
                if ( toggle ) {
                    toggle.setAttribute( 'aria-expanded', 'false' );
                    toggle.focus();
                }
                parents.forEach( function ( p ) { p.classList.remove( 'is-open' ); } );
            }
        } );

        // ── Reset on viewport resize ──────────────────────────
        window.addEventListener( 'resize', function () {
            if ( window.innerWidth >= 768 ) {
                if ( nav ) nav.classList.remove( 'is-open' );
                if ( toggle ) toggle.setAttribute( 'aria-expanded', 'false' );
                parents.forEach( function ( p ) { p.classList.remove( 'is-open' ); } );
            }
        } );
    } );
} )();
