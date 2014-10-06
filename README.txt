Plugin Be2bill
-------------------

Ce plugin permet d'ajouter le mode de paiement Be2bill (http://www.be2bill.com)
à Thelia.

Configuration
-------------

Le plugin est directement configurable dans le Back Office de Thelia :

Identifier : l'identifiant de votre compte Be2bill (ce n'est pas l'adresse email
dont vous vous servez pour accéder à votre espace Be2bill mais bien le compte associé).

Mot de passe : le mot de passe associé à ce compte.

Url : url fourni par Be2bill pour accéder à leur service de type : secure-test.be2bill.com.

3DSecure : Si vous voulez activer le mode 3DSECURE lors des paiements.

Description : Courte description du panier de la transaction (maximum 510 charactéres).

-------------

N'oubliez pas d'aller configurer votre compte dans l'extranet Be2bill :

1) Autoriser l'hébergeur à acceder au service Be2Bill en renseignant son adresse IP dans la configuration.

2) Renseigner les urls de redirections :

http://votresite.fr/be2bill/redirect/payment (Url après traitement)
http://votresite.fr/cart (Url en cas d'annulation)

3) Renseigner les urls de notifications, c'est cette page qui va passé votre commande en payée ou non :
http://votresite.fr/be2bill/callback/notif (notification de transactions)
http://votresite.fr/be2bill/callback/unpayed (notification d'impayés)