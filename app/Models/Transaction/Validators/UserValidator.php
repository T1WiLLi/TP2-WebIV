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

    public static function validateRegistration(array $formData): void
    {
        $form = new Form($formData);

        $form->field("username", [
            Rule::required("Le nom d'utilisateur est requis."),
            Rule::regex('/^[a-zA-Z0-9_-]{3,20}$/', "Le nom d'utilisateur doit être alphanumérique et comporter de 3 à 20 caractères.")
        ]);

        $form->field("password", [ // Add a regex later
            Rule::required("Le mot de passe est requis."),
        ]);

        $form->field("firstname", [
            Rule::required("Le prénom est requis.")
        ]);

        $form->field("lastname", [
            Rule::required("Le nom est requis.")
        ]);

        $form->field("email", [
            Rule::required("L'email est requis."),
            Rule::email("Le format de l'email est invalide.")
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

        $form->field("newPassword", [ // Add a regex later
            Rule::required("Le nouveau mot de passe est requis."),
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
