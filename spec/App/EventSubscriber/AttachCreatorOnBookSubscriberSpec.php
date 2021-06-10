<?php

namespace spec\App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Book\Book;
use App\EventSubscriber\AttachCreatorOnBookSubscriber;
use PhpSpec\ObjectBehavior;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class AttachCreatorOnBookSubscriberSpec extends ObjectBehavior
{
    function let(Security $security): void
    {
        $this->beConstructedWith($security);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(AttachCreatorOnBookSubscriber::class);
    }

    function it_is_a_subscriber(): void
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_subscribes_to_events(): void
    {
        $this::getSubscribedEvents()->shouldReturn([
            KernelEvents::VIEW => ['attachCreator', EventPriorities::PRE_WRITE],
        ]);
    }

    function it_attaches_creators(
        KernelInterface $kernel,
        Request $request,
        Security $security,
        UserInterface $user,
        Book $book
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_POST);
        $security->getUser()->willReturn($user);

        $book->setCreatedBy($user)->shouldBeCalled();

        $this->attachCreator(new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $book->getWrappedObject()
        ));
    }

    function it_does_nothing_when_resource_is_not_a_book(
        KernelInterface $kernel,
        Request $request,
        Security $security,
        UserInterface $user,
        \stdClass $book
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_POST);
        $security->getUser()->willReturn($user);

        $this->attachCreator(new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $book->getWrappedObject()
        ));
    }

    function it_does_nothing_on_put_method(
        KernelInterface $kernel,
        Request $request,
        Security $security,
        UserInterface $user,
        Book $book
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_PUT);
        $security->getUser()->willReturn($user);

        $book->setCreatedBy($user)->shouldNotBeCalled();

        $this->attachCreator(new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $book->getWrappedObject()
        ));
    }
}
