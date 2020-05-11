**api permettant de mettre à jour ou de créer des nouveaux produits depuis une plateforme externe **


## créer des nouveaux produits
 url = `http://localhost:8000/api/product/add`<br>
 method = post<br>
 enctype="multipart/form-data"<br>
  data = {<br>
	titre: string,<br>
	description : string,<br>
	quantite : integer,<br>
	prix :float,<br>
	genre :string, <br>
	fournisseur_id: integer<br>
};

---


## mettre à jour produits
 url = `http://localhost:8000/api/product/edit`<br>
 method = post<br>
 enctype="multipart/form-data"<br>
 data = {<br>
	titre: string,<br>
	description : string,<br>
	quantite : integer,<br>
	prix :float,<br>
	genre :string, <br>
	produit_id: integer<br>
};

-----------------

** 5 tests unitaire  **
<br>
1- Register User<br>
2- Login User<br>
3- Test de page index d'admin<br>
4- Nouveau Produit<br>
5- Nouveau Fournisseur<br>


----------------

** Accès Admin ****<br>
identifiant: admin<br>
mot de passe: Password123<br>

**    Accès Client   **<br>
identifiant: user<br>
mot de passe : Password123<br>

---------------

