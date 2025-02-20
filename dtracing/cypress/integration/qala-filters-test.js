describe( 'Test Qala Filters', () => {

	beforeEach(() => {
		cy.visit('/shop/');
	});

	it( 'Tests toggle for filters', () => {
		cy.get( '.qala-filters__tax-list__show-hide a' ).click();

		cy.get( '.qala-filters__container' )
		  .should( 'have.class', 'open' );

		cy.get( '.qala-filters__tax-list__show-hide a' ).click();

		cy.get( '.qala-filters__container' )
		  .should( 'not.have.class', 'open' );
	});

	it( 'Tests the soring', () => {
		cy.get( '.choices' ).click()
			.should( 'have.class', 'is-open' );

		cy.get( '.choices__item' ).first().click();

		cy.get( '.choices' )
		  .should( 'not.have.class', 'is-open' );
	});

	it( 'Tests the filters', () => {
		cy.get( '.qala-filters__tax-list__show-hide a' ).click();

		cy.get( '.qala-filters__container' )
		  .should( 'have.class', 'open' );

		cy.get( '.qala-filters__tax-list__terms__term:first label' ).click();

		cy.get( '.qala-filters__tax-list__active-terms a' )
		  .its( 'length' )
		  .should( 'eq', 1 );

		cy.get( '.qala-filters__tax-list__active-terms a' ).first().click();

		cy.get( '.qala-filters__tax-list__active-terms' )
		  .should( 'be.empty' );
	});

	it( 'Tests resetting the filters', () => {
		cy.get( '.qala-filters__tax-list__show-hide a' ).click();

		cy.get( '.qala-filters__container' )
		  .should( 'have.class', 'open' );

		cy.get( '.qala-filters__tax-list__terms__term:first label' ).click();

		cy.get( '.qala-filters__tax-list__active-terms a' )
		  .its( 'length' )
		  .should( 'eq', 1 );

		cy.get( '.qala-filters__tax-list__active-terms-container > .qala-filters__reset-filter-container > .reset-filter-button' ).first().click();

		cy.get( '.qala-filters__tax-list__active-terms' )
		  .should( 'be.empty' );
	});

});
