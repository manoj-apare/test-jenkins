# cypress_scheduler


This module provide the functionality of cron execution at a regular interval. The Purpose of
this module is to check shipment and order state and based on the state it perform's action
related to each order.

## Shipment State


In drupal "commerce_shipment" we are maintaining four states for each Shipmnet (i.e: "New",
"In progress","Shipped" and "Delivered")

1.New:

When a order is successfully placed from Drupal end but that has not been placed successfully
at Api end then the state of shipment remain's "New".

2.In progress:

When a order is successfully place from Drupal end and the data was successfully pushed to Api
for creation of order then the state changes form "New" to "In progress".

3.Shipped:

After the order creation at Vendor end when the vendor's starts processing the order i.e when
the vendor ship's the consignment using shipping vendor (i.e FEDEX, UPS, DHL) then they will
get Tracking Code. On cron run if we get Tracking code from Vendor End the we change the state
form "In progress"to "Shipped".

4.Delivered:

After getting the Tracking code till the order get's delivered all the data that we get from
the vendor end are being stored in shipment data column. Once the vendor Confirm's Order delivery
we again change the shipment state from "Shipped" to "Delivered".

## Vendor API's

For Cypress currently we are intracting with four different types
of vendor's. They are:

1. DigiKey.
2. HarteHanks.
3. Avnet.
4. CML.

For all of the vendor's mentioned above have different Soap Api that that we have consumed for
our system. The implementaion for vendor's api are written in cypress_store_vendor module inside
Vendors folder. Majorly we are using only three api from all numorous api present from vendor's
side. They are:
1. Api to check inventory.
2. Api to create Order at Vendor End.
3. Api to Track Shipment.
4. Api to Cancel Shipment.

## BackEnd Logic


The Order's that need to be placed to different vendors are based on rules that was given by client
and that is written in "Order Routing Configuration". Based On these Rule the order's are send to
different vendors classes where execution happens.

## Order State

In drupal "commerce_order" we are maintaining four states for each Order (i.e: "validation",
"In progress","Shipped" and "Delivered").

In Front End order state "validation" will be represented as "New".

Order State is dependent on Shipment state. A Order can have multiple shipment based on product's
they have purchased. A order state will change only when the product state changes. The Order
state will be the lowest of the shipment state.

For Example :-

Order "A" has two shipment i.e shipment "B" and Shipment "C". Now Shipment B state is "In progress"
and Shipment "C" state is "Delivered". In this case Order State will be "In progress". Which is
lowest in the shipment state.


