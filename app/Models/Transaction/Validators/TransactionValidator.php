<?php

namespace Models\Transaction\Validators;

use Models\Exceptions\FormException;
use Zephyrus\Application\Form;
use Zephyrus\Application\Rule;

class TransactionValidator
{
    public static function validateCreate(Form $form, string $userType): void
    {
        $form->field("name", [
            Rule::required("Le nom de l'article est requis."),
        ]);
        $form->field("price", [
            Rule::required("Le prix est requis."),
            Rule::decimal("Le prix doit être un nombre valide."),
            Rule::greaterThan(0, "Le prix doit être positif.")
        ]);
        $form->field("quantity", [
            Rule::required("La quantité est requise."),
            Rule::integer("La quantité doit être un entier positif."),
            Rule::greaterThan(0, "La quantité doit être supérieure à zéro.")
        ]);

        if ($userType === "NORMAL") {
            $form->field("price", [
                Rule::lowerEqualsThan(30, "Les utilisateurs NORMAL ne peuvent pas acheter des articles à plus de 30$.")
            ]);
        }

        if (!$form->verify()) {
            throw new FormException($form);
        }
    }
}
