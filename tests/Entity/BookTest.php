<?php

declare(strict_types=1);

use App\Entity\Book\Book;
use App\Entity\Author\Author;
use Doctrine\Common\Collections\Collection;

beforeEach(fn () => $this->book = new Book);

it('has no name by default', function () {
    expect($this->book->getName())->toBeNull();
    // or $this->assertNull($this->book->getName());
});

test('its name is mutable', function (string $name) {
    $this->book->setName($name);

    expect($this->book->getName())->toBe($name);
    // or $this->assertEquals($this->book->getName(), $name);
})->with([
    'Shining',
    'Lord of the rings',
]);

it('initializes authors collection by default', function () {
    expect($this->book->getAuthors())->toBeInstanceOf(Collection::class);
});

it('adds authors', function (Author $author) {
    $this->book->addAuthor($author);

    expect($this->book->getAuthors())
        ->toHaveCount(1)
        ->and($this->book->hasAuthor($author))->toBeTrue()
    ;
})->with([
    new Author(),
]);

it('removes authors', function (Author $author) {
    $this->book->addAuthor($author);
    $this->book->removeAuthor($author);

    expect($this->book->getAuthors())
        ->toHaveCount(0)
        ->and($this->book->hasAuthor($author))->toBeFalse()
    ;
})->with([
    new Author(),
]);
