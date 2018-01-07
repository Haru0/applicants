<?php

namespace Applicants;

use Cowsayphp\AbstractAnimal;

/**
 * Kitty class.
 *
 * @package Applicants
 */
class Kitty extends AbstractAnimal
{

    /**
     * @see https://github.com/bkendzior/cowfiles/blob/master/kitty.cow
     */
    protected $character = <<<DOC

{{bubble}}

       ("`-'  '-/") .___..--' ' "`-._
         ` o o  )    `-.   (      ) .`-.__. `)
         (_Y_.) ' ._   )   `._` ;  `` -. .-'
      _.. `--'_..-_/   /--' _ .' ,4
   ( i l ),-''  ( l i),'  ( ( ! .-'    
   
DOC;

}
