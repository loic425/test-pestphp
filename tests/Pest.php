<?php

declare(strict_types=1);

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Book\Book;
use App\EventSubscriber\AttachCreatorOnBookSubscriber;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

beforeEach(function () {
    $security = Mockery::mock(Security::class);

    $this->security = $security;
    $this->subscriber = new AttachCreatorOnBookSubscriber($security);
});

afterAll(fn () => Mockery::close());

it('is a subscriber', function () {
    expect($this->subscriber)->toBeInstanceOf(EventSubscriberInterface::class);
});

it('subscribes to events', function () {
    expect($this->subscriber::getSubscribedEvents())->toBeArray()->toBe([
        KernelEvents::VIEW => ['attachCreator', EventPriorities::PRE_WRITE],
    ]);
});

it('attaches creators', function (Book $book) {
    $request = Mockery::mock(Request::class);
    $kernel = Mockery::mock( KernelInterface::class);
    $user = Mockery::mock(UserInterface::class);

    $request->allows()->getMethod()->andReturns(Request::METHOD_POST);
    $this->security->allows()->getUser()->andReturns($user);

    $this->subscriber->attachCreator(new ViewEvent(
        $kernel,
        $request,
        HttpKernelInterface::MASTER_REQUEST,
        $book
    ));

    expect($book->getCreatedBy())->toBeInstanceOf(UserInterface::class);
})->with([
    new Book(),
]);

it('does nothing when resource is not a book', function (stdClass $book) {
    $request = Mockery::mock(Request::class);
    $kernel = Mockery::mock(KernelInterface::class);
    $user = Mockery::mock(UserInterface::class);

    $request->allows()->getMethod()->andReturns(Request::METHOD_POST);
    $this->security->allows()->getUser()->andReturns($user);

    $this->subscriber->attachCreator(new ViewEvent(
        $kernel,
        $request,
        HttpKernelInterface::MASTER_REQUEST,
        $book
    ));
})->with([
    new stdClass(),
]);

it('does nothing on put method', function (Book $book) {
    $request = Mockery::mock(Request::class);
    $kernel = Mockery::mock(KernelInterface::class);
    $user = Mockery::mock(UserInterface::class);

    $request->allows()->getMethod()->andReturns(Request::METHOD_PUT);
    $this->security->allows()->getUser()->andReturns($user);

    $this->subscriber->attachCreator(new ViewEvent(
        $kernel,
        $request,
        HttpKernelInterface::MASTER_REQUEST,
        $book
    ));

    expect($book->getCreatedBy())->toBeNull();
})->with([
    new Book(),
]);
