<?php

namespace spec\App\Entity\Book;

use App\Entity\Author\Author;
use App\Entity\Book\Book;
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;

class BookSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(Book::class);
    }

    function it_has_no_name_by_default(): void
    {
        $this->getName()->shouldBeNull();
    }

    function its_name_is_mutable(): void
    {
        $this->setName('Shining');

        $this->getName()->shouldBe('Shining');
    }

    function it_initializes_authors_collection_by_default(): void
    {
        $this->getAuthors()->shouldHaveType(Collection::class);
    }

    function it_adds_authors(Author $author): void
    {
        $this->addAuthor($author);

        $this->getAuthors()->shouldHaveCount(1);
        $this->hasAuthor($author)->shouldBe(true);
    }

    function it_removes_authors(Author $author): void
    {
        $this->addAuthor($author);
        $this->removeAuthor($author);

        $this->getAuthors()->shouldHaveCount(0);
        $this->hasAuthor($author)->shouldBe(false);
    }
}
