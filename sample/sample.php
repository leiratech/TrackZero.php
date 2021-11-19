<?php

$trackZeroClient = new TrackZeroClient("{Your Api Key Here}");
$trackZeroClient->create_analytics_space("php-sample-space");
$entity = new Entity("Order",7382)
        ->add_attribute("total value", 990)
        ->add_attribute("time", new \DateTime())
        ->add_entity_reference_attribute("Items", "Product", 1)
        ->add_entity_reference_attribute("Items", "Product", 6)
        ->add_entity_reference_attribute("Ship To", "Address", "888999123");
$trackZeroClient->upsert_entity("php", $e);88amp
$session = $trackZeroClient->create_analytics_space_session("php", 300);
// Redirect the user to the url stored in $session->url
$trackZeroClient->delete_entity("php", "Order", 7382);
$trackZeroClient->delete_analytics_space("php");