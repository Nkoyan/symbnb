<?php

namespace App\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class Pagination
{
    private $entityClass;
    private $limit = 10;
    private $currentPage;
    private $data;
    private $totalPages;

    public function __construct(
        private readonly ObjectManager $manager,
        private readonly Environment $twig,
        private readonly RequestStack $requestStack,
        private $templatePath
    ) {
    }

    public function getEntityClass()
    {
        return $this->entityClass;
    }

    public function setEntityClass($entityClass): self
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function setCurrentPage(int $currentPage): self
    {
        $this->currentPage = max(1, $currentPage);

        return $this;
    }

    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    public function setTemplatePath($templatePath): self
    {
        $this->templatePath = $templatePath;

        return $this;
    }

    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    /**
     * @throws \Exception
     */
    public function paginate(): self
    {
        if (!$this->entityClass) {
            throw new \Exception("Vous devez spécifier l'entité avec pagination->setEntityClass()");
        }

        if (!$this->currentPage) {
            throw new \Exception('Vous devez spécifier la page courante avec pagination->setCurrentPage()');
        }

        $repo = $this->manager->getRepository($this->entityClass);

        $total = \count($repo->findAll());
        $this->totalPages = ceil($total / $this->limit);
        $this->currentPage = min($this->currentPage, $this->totalPages);
        $offset = ($this->currentPage - 1) * $this->limit;

        $this->data = $repo->findBy([], [], $this->limit, $offset);

        return $this;
    }

    /**
     * @throws \Exception
     */
    public function getData(): array
    {
        if (!$this->entityClass) {
            throw new \Exception("Vous devez spécifier l'entité avec pagination->setEntityClass()");
        }

        if (!$this->currentPage) {
            throw new \Exception('Vous devez spécifier la page courante avec pagination->setCurrentPage()');
        }

        return $this->data;
    }

    public function display()
    {
        $this->twig->display($this->templatePath, [
            'current_page' => $this->currentPage,
            'total_pages' => $this->totalPages,
            'route_name' => $this->requestStack->getCurrentRequest()->attributes->get('_route'),
        ]);
    }
}
