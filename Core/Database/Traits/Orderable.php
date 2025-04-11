<?php

namespace Core\Database\Traits;

enum OrderingDirection: string
{
    case Asc = "ASC";
    case Dsc = "DSC";
}

trait Orderable
{
    /**
     * @param array<string, OrderingDirection> $orderFields
     */
    protected $orderFields = [];

    /**
     * @param string $field
     * @param OrderingDirection $dir
     * 
     * @return self
     */
    public function orderBy(string $field, $dir = OrderingDirection::Asc): self
    {
        $this->setOrderBy($field, $dir);
        return $this;
    }

    protected function setOrderBy($field, $dir)
    {
        $this->orderFields[$field] = $dir;
    }

    protected function buildOrders(): string
    {
        return empty($this->orderFields) ? "" : "ORDER BY" . implode(
            ", ",
            array_map(fn($field) => $this->buildOrder($field, $this->orderFields[$field]), $this->orderFields)
        );
    }

    protected function buildOrder($field, $order): string
    {
        return "{$field} {$order}";
    }
}
