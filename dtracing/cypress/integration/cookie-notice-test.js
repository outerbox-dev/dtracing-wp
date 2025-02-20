/**
 * Test Cookie Notice functionality.
 *
 * @author @viktor.froberg
 */
describe( 'Cookie Notice Test', () => {
	it( 'Check if the Cookie Notice is visible, if we can give consent and if the Cookie Notice disappears afterwards.', () => {
		// Go to the home page.
		cy.visit( '/' );

		// Make sure the Cookie Notie banner exists
		cy.get( '#custom-cookie-message-banner' ).should( 'exist' );

		// Click the cookie consent button.
		cy.get( '.custom-cookie-message-banner__close' ).click();

		// Make sure the Cookie Notie banner is removed
		cy.get( '#custom-cookie-message-banner' ).should( 'not.exist' );

		// Reload the page
		cy.reload();

		// Make sure the Cookie Notie banner is still gone.
		cy.get( '#custom-cookie-message-banner' ).should( 'not.exist' );
	} );
} );
