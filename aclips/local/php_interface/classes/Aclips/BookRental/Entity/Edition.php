<?php

namespace Aclips\BookRental\Entity;

/**
 * Class Edition
 * Сущность Издание
 * @package Aclips\BookRental\Entity
 */
class Edition
{
    /**
     * Идентификатор издания
     * @var int|null
     */
    public ?int $id = null;

    /**
     * Название издания
     * @var string|null
     */
    public ?string $name = null;

    /**
     * Год публикации
     * @var int|null
     */
    public ?int $publishingYear = null;

    /**
     * Идентификатор издательства
     * @var int|null
     */
    public ?int $publishingHouse = null;

    /**
     * Идентификатор жанра
     * @var int|null
     */
    public ?int $genre = null;

    /**
     * Идентификатор файла обложки
     * @var int|null
     */
    public ?int $bookCoverId = null;

    /**
     * Описание издания
     * @var string|null
     */
    public ?string $description = null;

    /**
     * Автор
     * @var string|null
     */
    public ?string $author = null;

}