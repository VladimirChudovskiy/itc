<?php

namespace Drupal\itc\Plugin\Field\FieldFormatter;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Field\Annotation\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;


/**
 * @FieldFormatter(
 *     id = "vote_default",
 *     label = @Translation("Vote"),
 *     field_types = {
 *          "vote"
 *     }
 * )
 */
class VoteDefaultFormatter extends FormatterBase {

    /**
     * Builds a renderable array for a field value.
     *
     * @param \Drupal\Core\Field\FieldItemListInterface $items
     *   The field values to be rendered.
     * @param string $langcode
     *   The language that should be used to render the field.
     *
     * @return array
     *   A renderable array for $items, as an array of child elements keyed by
     *   consecutive numeric indexes starting from 0.
     */
    public function viewElements(FieldItemListInterface $items, $langcode)
    {
//        die();
        $view_mode = $this->getSetting('view_mode');
        $elements = [

        ];
        $entity_type = $items->getEntity()->getEntityTypeId();
        $entity_id = $items->getEntity()->id();


        $elements = [];
        foreach ($items as $delta => $item){
            $markup = '<div class="stars-wrap" data-entity-type="'.$entity_type.'" data-entity-id="'.$entity_id.'" data-average="'.$item->value.'" data-field-name="'.$this->fieldDefinition->getName().'" >';
            foreach ([1,2,3,4,5] as $el){
                if($el<=$item->value){
                    $markup .= '<span class="star hover">★</span>';
                }else{
                    $markup .= '<span class="star">★</span>';
                }
            }
            $markup .= '</div>';

            $elements[$delta] = [
                '#type' => 'markup',
                '#markup' => $markup,
                '#attached' => ['library'=>['itc/itc']],
            ];
        }


        return $elements;
    }
}