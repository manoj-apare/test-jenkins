<?php

namespace Drupal\cypress_store_vendor\EventSubscriber;

use Drupal\cypress_store_vendor\CypressStoreVendor;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class CypressStoreVendorEventSubscriber.
 *
 * @package Drupal\cypress_store_vendor
 */
class CypressStoreVendorEventSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[CypressStoreVendor::ERROR][] = array('loggerEmail', 800);
    return $events;
  }

  /**
   * Subscriber Callback for the event.
   *
   * @param CypressStoreVendor $event
   *   Cypress store vendor error event object.
   */
  public function loggerEmail(CypressStoreVendor $event) {
    $message = $event->getMessage();
    \Drupal::logger('cypress_store_vendor')->error($message['subject'] . ' : ' . $message['body']);
    $mail_manager = \Drupal::service('plugin.manager.mail');
    $module = 'cypress_store_vendor';
    $key = 'logger';
    $to = \Drupal::config('system.site')->get('mail');
    $params['headers']['Bcc'] = 'disha.bhadra@valuebound.com, manoj.k@valuebound.com';
    $params['message'] = $message['body'];
    $params['title'] = $message['subject'];
    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $send = TRUE;
    $result = $mail_manager->mail($module, $key, $to, $langcode, $params, NULL, $send);
    if ($result['result'] !== TRUE) {
      drupal_set_message(t('There was a problem sending your message and it was not sent.'), 'error');
    }
    else {
      drupal_set_message(t('Your message has been sent.'));
    }
  }

}
