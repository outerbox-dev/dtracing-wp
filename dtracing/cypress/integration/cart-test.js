/**
 * Cypress tests!
 *
 * @author @richard.sweeney
 */
describe( 'WooCommerce Cart Test', () => {
	/**
	 * This is a bit fragile, but it's a decent enough start for Qala I think.
	 *
	 * The category URL is going to change of course, but I guess we just need
	 * to update that manually in that case.
	 *
	 * See cypress.json for more settings!
	 *
	 * @link https://docs.cypress.io/api/api/table-of-contents.html
	 */
	it( 'Tests adding a product to the cart, visiting the checkout page, removing the product and then returing to the shop page.', () => {
		// Visit this URL
		cy.visit( '/kategori/klader/tshirts/' );

		// Get the first product in the list
		cy.get( 'ul.products li:first' ).click();
		// Add 2 to the quantity picker
		cy.get( 'input.input-text.qty' ).clear().type( '2' );
		// Add it to the cart
		cy.get( 'button[name="add-to-cart"]' ).click();

		// Check the message exists and contains the correct text
		cy.get( '.woocommerce-message' )
		    .should( 'exist' )
			.should( 'contain', 'have been added to your cart' );

		// Click the link to open the mini-cart
		cy.get( '#site-header-cart' ).click();
		// Check the mini-cart is visible
		cy.get( '.widget.woocommerce.widget_shopping_cart' )
			.should( 'be.visible' );
		// Click the button to go the the checkout
		cy.get( '.button.checkout' ).click();

		// Check there is one item in the cart
		// (each time the test is run a new session is run, so the cart will always be empty at the start of the tests)
		cy.get( 'tr.woocommerce-cart-form__cart-item' )
			.its( 'length' )
			.should( 'eq', 1 );
		// Check the input field is correct
		cy.get( '.input-text.qty' )
			.should( 'have.value', '2' );

		// Remove the product from the cart
		cy.get( '.product-remove a' ).click();
		// Check the empty cart element exists
		cy.get( '.cart-empty' )
		    .should( 'exist' );
		// Check the return to shop button exists and contains the correct text
		cy.get( '.return-to-shop' )
			.should( 'exist' )
			.should( 'contain', 'Return to shop' );
	});
});
