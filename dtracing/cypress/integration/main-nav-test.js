/**
 * Test for main navigation
 *
 * @author @victor.camnerin
 */
describe( 'Main Navigation', () => {
	const menuItemUrl = '/shop';
	const subMenuItemLabel = 'Kläder';

	beforeEach( 'Visit Qala.se', () => {
		cy.visit( '/' );
	} );

	it( 'Desktop: Test hover and click', () => {
		// Check if the navbar is visible
		cy.get( '#site-navigation' ).should( 'be.visible' );

		/** Open mega menu
		 *
		 * Since the megamenu uses opacity and visibility to show and hide it's elements
		 * we have to check for those css values.
		 *
		 * Cypress has no good way of testing hover for this case so we manually have to
		 * add this open class to the megamenu element. This also means this test only actually
		 * tests the css and not the JS for the hover state.
		 */
		cy.get( '.megamenu' ).first()
			.then( menuItem => {
				menuItem[ 0 ].classList.add( 'open' );
			} )
			.find( '.sub-menu-container' )
			.should( subMenuContainer => {
				expect( subMenuContainer ).to.have.css( 'opacity', '1' );
			} )
			.contains( subMenuItemLabel );

		// Close mega menu
		cy.get( '.megamenu' ).first()
			.then( menuItem => {
				menuItem[ 0 ].classList.remove( 'open' );
			} )
			.find( '.sub-menu-container' )
			.should( subMenuContainer => {
				expect( subMenuContainer ).to.have.css( 'opacity', '0' );
			} );

		// Click navbar link
		cy.get( `.menu-item a[href="${ menuItemUrl }"]` ).click();

		// Should be on a new URL which includes '/shop'
		cy.url().should( 'include', menuItemUrl );
	} );

	it( 'Mobile: Open and close menu', () => {
		cy.viewport( 'iphone-6' );

		// Check if the navbar is visible
		cy.get( '#site-navigation' ).should( 'be.visible' );

		// Check if the mobile fold out menu is hidden
		cy.get( '.inner-menu-wrapper' ).should( 'be.hidden' );

		// Click to open menu
		cy.get( '#mobile-button' ).click();

		// Check if the mobile fold out menu is hidden
		cy.get( '.inner-menu-wrapper' ).should( 'be.visible' );

		// Click to close menu
		cy.get( '.close-mobile-nav' ).click();

		// Check if the mobile fold out menu is hidden
		cy.get( '.inner-menu-wrapper' ).should( 'be.hidden' );
	} );

	it( 'Mobile: Test page navigation', () => {
		cy.viewport( 'iphone-6' );

		cy.get( '#mobile-button' ).click();

		// Check if the mobile fold out menu is hidden
		cy.get( '.inner-menu-wrapper' ).should( 'be.visible' );

		// Click navbar link
		cy.get( `.menu-item a[href="${ menuItemUrl }"]` ).click();

		// Should be on a new URL which includes '/shop'
		cy.url().should( 'include', menuItemUrl );
	} );
} );
