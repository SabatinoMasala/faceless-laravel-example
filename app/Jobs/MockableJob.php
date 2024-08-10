<?php

namespace App\Jobs;

abstract class MockableJob
{
    abstract protected function mock();
    abstract protected function shouldMock(): bool;
    abstract protected function execute();

    protected function handleOrMock()
    {
        if ($this->shouldMock()) {
            return $this->mock();
        }
        return $this->execute();
    }

}
