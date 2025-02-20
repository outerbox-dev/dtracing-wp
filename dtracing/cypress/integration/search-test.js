describe( 'Search test', function() {
	it( 'Desktop: Testing search', function() {
		cy.visit( 'https://qala.se/' );

		cy.get( '.search-toggle' ).click();

		cy.focused().should( 'have.id', 's' );

		cy.get( '#s' )
			.type( 'Qala' )
			.should( 'have.value', 'Qala' )
			.type( '{enter}' );

		cy.get( 'h1' )
			.should( 'contain', 'Qala' );
	} );

	it( 'Mobile: Testing search', function() {
		cy.viewport( 'iphone-5' );

		cy.visit( 'https://qala.se/' );

		cy.get( '.search-toggle' ).click();

		cy.focused().should( 'have.id', 's' );

		cy.get( '#s' )
			.type( 'Qala' )
			.should( 'have.value', 'Qala' )
			.type( '{enter}' );

		cy.get( 'h1' )
			.should( 'contain', 'Qala' );
	} );
} );
