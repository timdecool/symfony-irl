<?php
namespace App\Twig\Components\Link;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent()]
class Primary
{
    public ?string $path = null;
    public string $content;
}