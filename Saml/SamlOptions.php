<?php

/*
 * This file is part of the SamlBundle.
 *
 * (c) Paulo Dias <dias.paulo@gmail.com>
 *
 */
namespace PDias\SamlBundle\Saml;

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Handles the options of firewall
 *
 * @package    SamlBundle
 * @subpackage Saml
 * @author     Paulo Dias <dias.paulo@gmail.com>
 */
class SamlOptions
{
    protected $options;

    public function __construct(array $options = [])
    {
        $this->options = new ParameterBag($options);
    }
    
    public function getOptions()
    {
        return $this->options;
    }
    
    public function getOption($optionId)
    {
        return $this->options->get($path);
    }
}
