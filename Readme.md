Plugin Be2Bill (FR)
-------------------

Ce plugin permet d'ajouter le mode de paiement be2bill (http://www.be2bill.com)
à Thelia.

Configuration
-------------

Le plugin est directement configurable dans le Back Office de Thelia :

Identifier : l'identifiant de votre compte be2bill (ce n'est pas l'adresse email
dont vous vous servez pour accéder à votre espace be2bill mais bien le compte associé).

Mot de passe : le mot de passe associé à ce compte.

Url : URL fournie par be2bill pour accéder à leur service de type : secure-test.be2bill.com.

3DSecure : Si vous voulez activer le mode 3DSECURE lors des paiements.

Description : Courte description du panier de la transaction (maximum 510 charactéres).

Le module be2bill peut également être configuré pour utiliser Paypal. Il faut pour cela activer
ce type de compte dans l'intranet be2bill. Une fois activé, il faudra activer l'option Paypal
dans la page de configuration du module et renseigner les identifiant et mot de passe donnés par be2bill
pour Paypal.


-------------

N'oubliez pas d'aller configurer votre compte dans l'extranet be2bill :

1) Autoriser l'hébergeur à acceder au service be2bill en renseignant son adresse IP dans la configuration.

2) Renseigner les urls de notifications, c'est cette page qui va passer votre commande en payée ou non :
   http://votresite.fr/be2bill/callback/payment (notification de transactions)
   http://votresite.fr/be2bill/callback/unpaid (notification d'impayés)

3) Renseigner les urls de redirections :
    http://votresite.fr/be2bill/redirect/payment (Url après traitement)
    http://votresite.fr/be2bill/redirect/cancel (Url en cas d'annulation)

*Les URLs sont les mêmes pour les comptes "carte bancaire" et "PayPal".*

------------------------------------------------------------------------------------------------------------------------

be2bill plugin (EN)
-------------------

This plugin adds be2bill payment method (http://www.be2bill.com)

Configuration
-------------

This plugin is configurable in thelia back office :

Identifier : be2bill account identifier (this is not the email).

Mot de passe : be2bill password.

Url : Web service url given by be2bill, exemple : "secure-magenta.be2bill.com" .

3DSecure : if you want active 3DSecure for payment

Description : Description of the transaction (string / 510 char max).

The be2bill module can be used with Paypal account.
First, you should activate the Paypal account on your be2bill account.
Next you have to activate the option in the configuration page of the module and set the
PayPal identifier and password send by be2bill.

-------------

Don't forget to configure your account in be2bill extranet :

1) Allow the host to access the service by filling its IP address in the be2bill configuration.

2) Fill in the notifications urls :
   http://site.com/be2bill/callback/payment (notification of transactions)
   http://site.com/be2bill/callback/unpaid (unpaid notification)

3) Fill in the redirect urls :
   http://site.com/be2bill/redirect/payment ( Url after treatment)
   http://site.com/be2bill/redirect/cancel ( Cancel url)

*Urls are similar for "Card" and "PayPal" accounts.*

### Todo

- use hooks instead of admin includes 
- use ModuleConfig instead of Be2billConfig
- improve back office UI : display log, separate transactions and config.

