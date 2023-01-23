<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once __DIR__ . '/../src/Impulse/Recommender/Dataset.php';
include_once __DIR__ . '/../src/Impulse/Recommender/LearningModel.php';
include_once __DIR__ . '/../src/Impulse/Recommender/Trainer.php';
include_once __DIR__ . '/../src/Impulse/Recommender/Builder.php';

$dataset = new Impulse\Recommender\Dataset();

$dataset->addItem(Impulse\Recommender\Dataset\Item::create('The Dark Knight'));
$dataset->addItem(Impulse\Recommender\Dataset\Item::create('Guardians of the Galaxy'));
$dataset->addItem(Impulse\Recommender\Dataset\Item::create('Logan'));
$dataset->addItem(Impulse\Recommender\Dataset\Item::create('Forrest Gump'));
$dataset->addItem(Impulse\Recommender\Dataset\Item::create('The Kid'));

$dataset->addCategory(Impulse\Recommender\Dataset\Category::create('Anna'));
$dataset->addCategory(Impulse\Recommender\Dataset\Category::create('Barbara'));
$dataset->addCategory(Impulse\Recommender\Dataset\Category::create('Charlie'));
$dataset->addCategory(Impulse\Recommender\Dataset\Category::create('Dave'));

$dataset->addRating(Impulse\Recommender\Dataset\Rating::create('The Dark Knight', 'Anna', 0));
$dataset->addRating(Impulse\Recommender\Dataset\Rating::create('The Dark Knight', 'Barbara', 0));
$dataset->addRating(Impulse\Recommender\Dataset\Rating::create('The Dark Knight', 'Charlie', 5));
$dataset->addRating(Impulse\Recommender\Dataset\Rating::create('The Dark Knight', 'Dave', 5));
$dataset->addRating(Impulse\Recommender\Dataset\Rating::create('Guardians of the Galaxy', 'Anna', 0));
$dataset->addRating(Impulse\Recommender\Dataset\Rating::create('Guardians of the Galaxy', 'Barbara', null));
$dataset->addRating(Impulse\Recommender\Dataset\Rating::create('Guardians of the Galaxy', 'Charlie', null));
$dataset->addRating(Impulse\Recommender\Dataset\Rating::create('Guardians of the Galaxy', 'Dave', 5));
$dataset->addRating(Impulse\Recommender\Dataset\Rating::create('Logan', 'Anna', null));
$dataset->addRating(Impulse\Recommender\Dataset\Rating::create('Logan', 'Barbara', 0));
$dataset->addRating(Impulse\Recommender\Dataset\Rating::create('Logan', 'Charlie', 4));
$dataset->addRating(Impulse\Recommender\Dataset\Rating::create('Logan', 'Dave', null));
$dataset->addRating(Impulse\Recommender\Dataset\Rating::create('Forrest Gump', 'Anna', 4));
$dataset->addRating(Impulse\Recommender\Dataset\Rating::create('Forrest Gump', 'Barbara', 5));
$dataset->addRating(Impulse\Recommender\Dataset\Rating::create('Forrest Gump', 'Charlie', 0));
$dataset->addRating(Impulse\Recommender\Dataset\Rating::create('Forrest Gump', 'Dave', 0));
$dataset->addRating(Impulse\Recommender\Dataset\Rating::create('The Kid', 'Anna', 5));
$dataset->addRating(Impulse\Recommender\Dataset\Rating::create('The Kid', 'Barbara', 5));
$dataset->addRating(Impulse\Recommender\Dataset\Rating::create('The Kid', 'Charlie', 0));
$dataset->addRating(Impulse\Recommender\Dataset\Rating::create('The Kid', 'Dave', 0));

$model = new Impulse\Recommender\LearningModel($dataset, [
    'numFeatures' => 2
]);

$trainer = new Impulse\Recommender\Trainer($model, [
    'learningRate' => 0.01,
    'iterations' => 30000,
    'verbose' => true,
    'verboseStep' => 1000
]);

$trainer->train();

$builder = new Impulse\Recommender\Builder($model);
$builder->save(__DIR__, 'save1');