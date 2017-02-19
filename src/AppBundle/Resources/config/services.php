<?php 
// src/AppBundle/Resources/config/services.php
use AppBundle\Form\AuthorType;

$definition = new Definition(AuthorType::class, array(
    new Reference('doctrine.orm.entity_manager'),
));
$container
    ->setDefinition(
        'app.form.type.author',
        $definition
    )
    ->addTag('form.type');
