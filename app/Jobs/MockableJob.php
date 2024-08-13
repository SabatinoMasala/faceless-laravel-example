<?php

namespace App\Jobs;

abstract class MockableJob
{
    abstract protected function mock();
    abstract protected function shouldMock(): bool;

    protected function handleOrMock()
    {
        if ($this->shouldMock()) {
            return $this->mock();
        }
        return app()->call([$this, 'execute']);
    }

}
