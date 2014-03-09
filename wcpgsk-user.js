/*
 * Please paste your custom functions for WooCommerce checkout here
 * You may use this file to run your own client side validation for the checkout form
 
 * Example code to fill a select option:

var _postServiceTerminals = [
		{id: '1', name: 'Rīga, Pasta Stacija 101, A. Saharova iela 20a (Maxima XX)'},{id: '6', name: 'Rīga, Pasta Stacija 106, Āzenes iela 5 (T/C Olimpia)'},{id: '24', name: 'Rīga, Pasta Stacija 124, Brīvības 434b (T/C Juglas Centrs)'},{id: '39', name: 'Rīga, Pasta Stacija 139, Dzirnavu iela 67 (Galleria Riga)'},{id: '2', name: 'Rīga, Pasta Stacija 102, K. Ulmaņa gatve 88a (Maxima XXX)'},{id: '42', name: 'Rīga, Pasta Stacija 142, Krasta iela 46 (T/C MOLS)'},{id: '29', name: 'Rīga, Pasta Stacija 129, Mūkusalas iela 95 (T/C IKI)'},{id: '40', name: 'Rīga, Pasta Stacija 140, Rīga, Stacijas laukums 2 (T/C Origo)'},{id: '3', name: 'Rīga, Pasta Stacija 103, Slokas iela 115 (Maxima XXX)'},{id: '7', name: 'Rīga, Pasta Stacija 107, Stirnu iela 26'},{id: '15', name: 'Rīga, Pasta Stacija 115, Tilta iela 32 (Rimi)'},{id: '30', name: 'Aizkraukle, Pasta Stacija 130, Spīdolas iela 17 (T/C SuperNetto)'},{id: '27', name: 'Alūksne, Pasta Stacija 127, Pils iela 9b (t/c Maxima)'},{id: '21', name: 'Bauska, Pasta Stacija 121, Pionieru iela 2 (Rimi)'},{id: '31', name: 'Cēsis, Pasta Stacija 131, Lapsu iela 23 (Mego)'},{id: '17', name: 'Daugavpils, Pasta Stacija 117, Rīgas iela 9 (t/c SOLO)'},{id: '26', name: 'Dobele, Pasta Stacija 126, Muldavas iela 3a (T/C Forums)'},{id: '36', name: 'Gulbene, Pasta Stacija 136, Skolas iela 7 (t/c Maxima X)'},{id: '16', name: 'Jēkabpils, Pasta Stacija 116, Vienības iela 1a, T/C "MEGO"'},{id: '8', name: 'Jelgava, Pasta Stacija 108, Katoļu iela 7, (Kanclera nams)'},{id: '20', name: 'Jūrmala, Pasta Stacija 120, Artilērijas iela 2 (IKI)'},{id: '28', name: 'Ķekava, Pasta Stacija 128, Rīgas iela 22 (T/C Liiba)'},{id: '22', name: 'Kuldīga, Pasta Stacija 122, Smilšu iela 20 (ELVI)'},{id: '4', name: 'Liepāja, Pasta Stacija 104, K. Zāles laukums 8 (T/C Ostmala)'},{id: '41', name: 'Liepāja, Pasta Stacija 141, Klaipēdas iela 62 (t/c XL Sala)'},{id: '32', name: 'Limbaži, Pasta Stacija 132, Stacijas iela 8 (T/C Maxima XX)'},{id: '25', name: 'Madona, Pasta Stacija 125, Rūpniecības iela 49 (Maxima XX)'},{id: '18', name: 'Ogre, Pasta Stacija 118, Rīgas iela 23 (T/C Dauga)'},{id: '38', name: 'Preiļi, Pasta Stacija 138, Rēzeknes iela 4a (Maxima X)'},{id: '14', name: 'Rēzekne, Pasta Stacija 114, Pulkveža Brieža iela 26 (Maxima X)'},{id: '5', name: 'Saldus, Pasta Stacija 105, Striķu iela 10c (T/C Akvārijs)'},{id: '12', name: 'Sigulda, Pasta Stacija 112, Vidzemes šoseja 16 (T/C Raibais suns)'},{id: '23', name: 'Talsi, Pasta Stacija 123, Rīgas iela 8 (Maxima XX)'},{id: '19', name: 'Tukums, Pasta Stacija 119, Pasta iela 14 (T/C Rimi)'},{id: '10', name: 'Valka, Pasta Stacija 110, Ausekļa iela 54 (T/C Walk)'},{id: '9', name: 'Valmiera, Pasta Stacija 109, Rīgas iela 4 (T/C Valleta)'},{id: '13', name: 'Ventspils, Pasta Stacija 113, Lielais prospekts 3/5 (T/C Tobago)'}
	];

	var _postServiceTerminalsSelect = document.getElementById('billing_atestsel');
	for (var _ps_index = 0; _ps_index < _postServiceTerminals.length; _ps_index++ ) {
		var _psTerminal = _postServiceTerminals[_ps_index];
		var _postServiceOption = document.createElement('option');
		_postServiceOption.value = _psTerminal.id;
		_postServiceOption.text = _psTerminal.name;

		try {
		  _postServiceTerminalsSelect.add(_postServiceOption, _postServiceTerminalsSelect.options[null]);
		}
		catch (e)
		{
		  _postServiceTerminalsSelect.add(_postServiceOption, null);
		}
	}
*/