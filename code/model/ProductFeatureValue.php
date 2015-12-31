<?php

/**
 * Pivot table. Connects products with features, but also includes a value.
 *
 * @package shop_comparsion
 */
class ProductFeatureValue extends DataObject
{

    private static $db = array(
        "Value" => "Varchar",
        "Sort" => 'Int'
    );

    private static $default_sort = 'Sort ASC';

    private static $has_one = array(
        "Product" => "Product",
        "Feature" => "Feature"
    );

    private static $summary_fields    =  array(
        "Feature.Title" => "Feature",
        "Value" => "Value",
        "Feature.Unit" => "Unit"
    );

    private static $singular_name = "Feature";

    private static $plural_name = "Features";

    public function getCMSFields()
    {
        $fields = new FieldList();
        $feature = $this->Feature();

        if ($feature->exists()) {
            $fields->push(ReadonlyField::create("FeatureTitle", "Feature", $feature->Title));
            $fields->push($feature->getValueField());
        } else {
            $selected = Feature::get()
                ->innerJoin("ProductFeatureValue", "Feature.ID = ProductFeatureValue.FeatureID")
                ->filter("ProductFeatureValue.ProductID", Controller::curr()->currentPageID())
                ->getIDList();
            $features = Feature::get()->filter("ID:not", $selected);
            $fields->push(DropdownField::create("FeatureID", "Feature", $features->map()->toArray()));
            $fields->push(LiteralField::create("creationnote", "<p class=\"message\">You can choose a value for this feature after saving.</p>"));
        }
        
        return $fields;
    }

    public function getTitle()
    {
        return $this->Feature()->Title;
    }

    public function TypedValue()
    {
        return $this->Feature()->getValueDBField($this->Value);
    }
}
