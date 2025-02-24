<?php

namespace Models\Transaction\Validators;

use Models\Exceptions\FormException;
use Zephyrus\Application\Form;
use Zephyrus\Application\Rule;

class UserValidator
{
    public static function validateLogin(Form $form): void
    {
        $form->field("username", [
            Rule::required("Le nom d'utilisateur est requis.")
        ]);
        $form->field("password", [
            Rule::required("Le mot de passe est requis.")
        ]);

        if (!$form->verify()) {
            throw new FormException($form);
        }
    }

    public static function validateProfile(Form $form): void
    {
        $form->field("firstname", [
            Rule::required("Le prénom est requis.")
        ]);
        $form->field("lastname", [
            Rule::required("Le nom est requis.")
        ]);
        $form->field("email", [
            Rule::required("Le courriel est requis."),
            Rule::email("Le courriel n'est pas valide.")
        ]);
        $form->field("username", [
            Rule::required("Le nom d'utilisateur est requis.")
        ]);

        if (!$form->verify()) {
            throw new FormException($form);
        }
    }

    public static function validatePasswordChange(Form $form): void
    {
        $form->field("oldPassword", [
            Rule::required("L'ancien mot de passe est requis.")
        ]);

        $form->field("newPassword", [
            Rule::required("Le nouveau mot de passe est requis."),
            Rule::regex(
                "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}$/",
                "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule et un chiffre."
            )
        ]);

        if (!$form->verify()) {
            throw new FormException($form);
        }
    }

    public static function validateCredit(Form $form): void
    {
        $form->field("credit", [
            Rule::required("Le montant est requis."),
            Rule::decimal("Le montant doit être un nombre."),
            Rule::greaterThan(0, "Le montant doit être positif.")
        ]);

        if (!$form->verify()) {
            throw new FormException($form);
        }
    }
}
