<?php

/**
 * (c) Mell M. Zamora <rzproject.org> and creadits to the original author Antoine Berranger <antoine@ihqs.net>
 * (c) Laszlo Horvath <pentarim@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */


namespace Rz\ContactBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
// use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher;

use Rz\ContactBundle\Model\ContactInterface;
use Rz\ContactBundle\Manager\ContactManagerInterface;
use Rz\ContactBundle\Event;
use Rz\ContactBundle\Event\Events;

class ContactFormHandler
{
    protected $form;
    protected $request;
    protected $contactManager;
    protected $eventDispatcher;

    public function __construct(Form $form, Request $request, ContactManagerInterface $contactManager, EventDispatcher $eventDispatcher)
    {
        $this->form            = $form;
        $this->request         = $request;
        $this->contactManager  = $contactManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function process(ContactInterface $contact)
    {
        if (null === $contact) {
            $contact = $this->contactManager->createContact();
        }

        $this->form->setData($contact);

        $this->form->bindRequest($this->request);

        if ($this->form->isValid()) {
            $this->onSuccess($contact);

            return true;
        }

        return false;
    }

    protected function onSuccess(ContactInterface $contact)
    {
        //$event = new Event($contact, 'form.contact_submission');
        //$this->eventDispatcher->dispatch('rz_contact.contact_submission',$event);

        $this->eventDispatcher->dispatch(Events::onContactRequest, new Event\ContactEvent($contact));


        //$this->userManager->updateUser($user);
    }
}
