<?php
/**
 * Created by PhpStorm.
 * User: 67517
 * Date: 27.07.2016
 * Time: 15:12
 */

namespace nwlBundle\Validator;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DomainConstraintValidator extends ConstraintValidator
{


    public function validate($value, Constraint $constraint)
    {
        if($value === "" || $value == null){
            $this->context->addViolation($constraint->message);
        }

//        if (!preg_match('/^[a-zA-Z0-9]+$/', $value, $matches)) {
//            $this->context->buildViolation($constraint->message)
//                ->setParameter('%string%', $value)
//                ->addViolation();
//        }


        }


}