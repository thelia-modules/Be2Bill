<?php
/**
 * Created by PhpStorm.
 * User: vincent
 * Date: 02/10/2014
 * Time: 13:49
 */

namespace Be2Bill\Form;


use Be2Bill\Be2Bill;
use Be2Bill\Model\Be2billConfigQuery;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

class ConfigForm extends BaseForm
{

    /**
     *
     * in this function you add all the fields you need for your Form.
     * Form this you have to call add method on $this->formBuilder attribute :
     *
     * $this->formBuilder->add("name", "text")
     *   ->add("email", "email", array(
     *           "attr" => array(
     *               "class" => "field"
     *           ),
     *           "label" => "email",
     *           "constraints" => array(
     *               new \Symfony\Component\Validator\Constraints\NotBlank()
     *           )
     *       )
     *   )
     *   ->add('age', 'integer');
     *
     * @return null
     */
    protected function buildForm()
    {
        $translator = Translator::getInstance();

        $this->formBuilder
            ->add(
                'identifier',
                'text',
                array(
                    'constraints'   => array(new NotBlank()),
                    'required'      => true,
                    'label'         => $translator->trans('Identifiant du compte Be 2 Bill', array(), Be2Bill::MODULE_DOMAIN),
                    'data'          => Be2billConfigQuery::read('identifier'),
                    'label_attr'    => array('help' => $translator->trans('Identifiant du compte', array(), Be2Bill::MODULE_DOMAIN))
                )
            )
            ->add(
                'password',
                'text',
                array(
                    'label' => $translator->trans('Mot de passe', array(), Be2Bill::MODULE_DOMAIN),
                    'data' => Be2billConfigQuery::read('password'),
                    'label_attr' => array('help' => $translator->trans('Mot de passe Be2Bill', array(), Be2Bill::MODULE_DOMAIN))
                )
            )
            ->add(
                'description',
                'textarea',
                array(
                    'label' => $translator->trans('Description', array(), Be2Bill::MODULE_DOMAIN),
                    'data' => Be2billConfigQuery::read('description', 'Commande Be2Bill'),
                    'label_attr' => array('help' =>  $translator->trans('Description du panier de la transaction / chaîne 510 max', array(), Be2Bill::MODULE_DOMAIN))
                )
            )
            ->add(
                'url',
                'text',
                array(
                    'label' => 'Url',
                    'constraints' => array(new NotBlank()),
                    'data' => Be2billConfigQuery::read('url', $translator->trans('[type d\'environnement].be2bill.com', array(), Be2Bill::MODULE_DOMAIN)),
                    'required' => true,
                    'label_attr' => array('help' => $translator->trans('Url Web Service fournis par be2bill, exemple : "secure-magenta.be2bill.com" .', array(), Be2Bill::MODULE_DOMAIN))
                )
            )
            ->add(
                '3dsecure',
                'text',
                array(
                    'label' => '3DSecure',
                    'data' => Be2billConfigQuery::read('3dsecure', 0)
                )
            )
        ;
    }

    /**
     * @return string the name of you form. This name must be unique
     */
    public function getName()
    {
        return "be2bill_configuration_form";
    }
}
