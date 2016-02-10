Plugin Be2Bill (FR)
-------------------

Ce plugin permet d'ajouter le mode de paiement Be2Bill (http://www.be2bill.com)
à Thelia.

Configuration
-------------

Le plugin est directement configurable dans le Back Office de Thelia :

Identifier : l'identifiant de votre compte Be2bill (ce n'est pas l'adresse email
dont vous vous servez pour accéder à votre espace Be2bill mais bien le compte associé).

Mot de passe : le mot de passe associé à ce compte.

Url : URL fournie par Be2bill pour accéder à leur service de type : secure-test.be2bill.com.

3DSecure : Si vous voulez activer le mode 3DSECURE lors des paiements.

Description : Courte description du panier de la transaction (maximum 510 charactéres).

-------------

N'oubliez pas d'aller configurer votre compte dans l'extranet Be2bill :

1) Autoriser l'hébergeur à acceder au service Be2Bill en renseignant son adresse IP dans la configuration.

2) Renseigner les urls de notifications, c'est cette page qui va passer votre commande en payée ou non :
   http://votresite.fr/be2bill/callback/payment (notification de transactions)
   http://votresite.fr/be2bill/callback/unpaid (notification d'impayés)

3) Renseigner les urls de redirections :
    http://votresite.fr/be2bill/redirect/payment (Url après traitement)
    http://votresite.fr/be2bill/redirect/cancel (Url en cas d'annulation)

------------------------------------------------------------------------------------------------------------------------

Be2Bill plugin (EN)
-------------------

This plugin adds Be2Bill payment method (http://www.be2bill.com)

Configuration
-------------

This plugin is configurable in thelia back office :

Identifier : Be2Bill account identifier (this is not the email).

Mot de passe : Be2Bill password.

Url : Web service url given by Be2Bill, exemple : "secure-magenta.be2bill.com" .

3DSecure : if you want active 3DSecure for payment

Description : Description of the transaction (string / 510 char max).

-------------

Don't forget to configure your account in Be2Bill extranet :

1) Allow the host to access the service by filling its IP address in the Be2bill configuration.

2) Fill in the notifications urls :
   http://site.com/be2bill/callback/payment (notification of transactions)
   http://site.com/be2bill/callback/unpaid (unpaid notification)

3) Fill in the redirect urls :
   http://site.com/be2bill/redirect/payment ( Url after treatment)
   http://site.com/be2bill/redirect/cancel ( Cancel url)