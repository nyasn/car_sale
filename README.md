**api permettant de mettre à jour ou de créer des nouveaux produits depuis une plateforme externe **


## créer des nouveaux produits
 url = http://localhost:8000/api/product/add
 methode = post
 enctype="multipart/form-data"
 parametre = {
	titre: string,
	description : string,
	quantite : integer,
	prix :float,
	genre :string, 
	fournisseur_id: integer
};

---


## mettre à jour produits
 url = http://localhost:8000/api/product/edit
 methode = post
 enctype="multipart/form-data"
 parametre = {
	titre: string,
	description : string,
	quantite : integer,
	prix :float,
	genre :string, 
	produit_id: integer
};

---

** 5 tests unitaire  **

1- Register User
2- Login User
3- Test de page index d'admin
4- Nouveau Produit
5- Nouveau Fournisseur


