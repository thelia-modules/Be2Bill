<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/


namespace Be2Bill\Service;

use Be2Bill\Be2Bill;
use Thelia\Core\Translation\Translator;

/**
 * Class ExecCodeService
 * @package Be2Bill\Service
 * @author Julien Chanséaume <julien@thelia.net>
 */
class ExecCodeService
{
    /** @var Translator $translator */
    protected $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function getExecCodeList()
    {
        $execCodeList = [
            // to be translatable from backoffice
            // ->trans("Opération réussie")
            "0000" => "Opération réussie",
            // ->trans("Identification 3DSecure requise")
            "0001" => "Identification 3DSecure requise",
            // ->trans("Redirection requise pour finaliser une transaction")
            "0002" => "Redirection requise pour finaliser une transaction",
            // ->trans("Transaction en cours, notification en attente")
            "0003" => "Transaction en cours, notification en attente",
            // ->trans("Paramètre manquant")
            "1001" => "Paramètre manquant",
            // ->trans("Paramètre invalide")
            "1002" => "Paramètre invalide",
            // ->trans("Erreur HASH")
            "1003" => "Erreur HASH",
            // ->trans("Protocole non supporté")
            "1004" => "Protocole non supporté",
            // ->trans("Requête incorrecte, consultez la documentation pour construire la requête POST")
            "1005" => "Requête incorrecte, consultez la documentation pour construire la requête POST",
            // ->trans("Paramètres GET interdits")
            "1006" => "Paramètres GET interdits",
            // ->trans("Alias non trouvé")
            "2001" => "Alias non trouvé",
            // ->trans("Transaction de référence non trouvée")
            "2002" => "Transaction de référence non trouvée",
            // ->trans("Transaction de référence non aboutie")
            "2003" => "Transaction de référence non aboutie",
            // ->trans("Transaction de référence non remboursable")
            "2004" => "Transaction de référence non remboursable",
            // ->trans("Autorisation de référence non capturable")
            "2005" => "Autorisation de référence non capturable",
            // ->trans("Transaction de référence non terminée")
            "2006" => "Transaction de référence non terminée",
            // ->trans("Montant de capture invalide")
            "2007" => "Montant de capture invalide",
            // ->trans("Montant de remboursement invalide")
            "2008" => "Montant de remboursement invalide",
            // ->trans("Autorisation expirée")
            "2009" => "Autorisation expirée",
            // ->trans("échéancier non trouvé")
            "2010" => "échéancier non trouvé",
            // ->trans("échéancier déjà interrompu")
            "2011" => "échéancier déjà interrompu",
            // ->trans("échéancier déjà terminé")
            "2012" => "échéancier déjà terminé",
            // ->trans("Compte désactivé")
            "3001" => "Compte désactivé",
            // ->trans("Adresse IP serveur non autorisée")
            "3002" => "Adresse IP serveur non autorisée",
            // ->trans("Transaction non permise")
            "3003" => "Transaction non permise",
            // ->trans("Limite du débit de transactions dépassé")
            "3004" => "Limite du débit de transactions dépassé",
            // ->trans("Transaction refusée par le réseau bancaire")
            "4001" => "Transaction refusée par le réseau bancaire",
            // ->trans("Fonds insuffisants")
            "4002" => "Fonds insuffisants",
            // ->trans("Carte refusée par le réseau bancaire")
            "4003" => "Carte refusée par le réseau bancaire",
            // ->trans("Transaction abandonnée")
            "4004" => "Transaction abandonnée",
            // ->trans("Suspicion de fraude")
            "4005" => "Suspicion de fraude",
            // ->trans("Carte déclarée perdue")
            "4006" => "Carte déclarée perdue",
            // ->trans("Carte déclarée volée")
            "4007" => "Carte déclarée volée",
            // ->trans("Authentification 3DSecure échouée")
            "4008" => "Authentification 3DSecure échouée",
            // ->trans("Authentification 3DSecure expirée")
            "4009" => "Authentification 3DSecure expirée",
            // ->trans("Transaction invalide")
            "4010" => "Transaction invalide",
            // ->trans("Transaction doublon")
            "4011" => "Transaction doublon",
            // ->trans("Données de carte invalides")
            "4012" => "Données de carte invalides",
            // ->trans("Transaction non autorisée par le réseau bancaire pour ce porteur")
            "4013" => "Transaction non autorisée par le réseau bancaire pour ce porteur",
            // ->trans("Carte non-enrôlée 3D Secure")
            "4014" => "Carte non-enrôlée 3D Secure",
            // ->trans("Transaction expirée")
            "4015" => "Transaction expirée",
            // ->trans("Transaction refusée par le terminal de paiement")
            "4016" => "Transaction refusée par le terminal de paiement",
            // ->trans("Dépassement de la date d'expiration du formulaire (renseignée par le marchand)")
            "4017" => "Dépassement de la date d'expiration du formulaire (renseignée par le marchand)",
            // ->trans("Erreur protocole d'échange")
            "5001" => "Erreur protocole d'échange",
            // ->trans("Erreur réseau bancaire")
            "5002" => "Erreur réseau bancaire",
            // ->trans("Système en maintenance")
            "5003" => "Système en maintenance",
            // ->trans("Délai dépassé, la réponse arrivera par URL de notification")
            "5004" => "Délai dépassé, la réponse arrivera par URL de notification",
            // ->trans("Erreur d'affichage du module 3D Secure")
            "5005" => "Erreur d'affichage du module 3D Secure",
            // ->trans("Transaction refusée par le marchand")
            "6001" => "Transaction refusée par le marchand",
            // ->trans("Transaction refusée")
            "6002" => "Transaction refusée",
            // ->trans("Le porteur a déjà contesté une transaction")
            "6003" => "Le porteur a déjà contesté une transaction",
            // ->trans("Transaction refusée par les règles du marchand et/ou de la plateforme")
            "6004" => "Transaction refusée par les règles du marchand et/ou de la plateforme",
        ];

        return $execCodeList;
    }

    public function getTitle($execCode)
    {
        $execCodeList = $this->getExecCodeList();
        if (isset($execCodeList[$execCode])) {
            return self::trans($execCodeList[$execCode]);
        }

        return $this->trans('Unknown exec. code');
    }

    protected function trans($id, $parameters = [])
    {
        if (null === $this->translator) {
            $this->translator = Translator::getInstance();
        }

        return $this->translator->trans($id, $parameters, Be2Bill::MODULE_DOMAIN);
    }
}