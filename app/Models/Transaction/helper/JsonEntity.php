<?php

namespace Models\Transaction\helper;

interface JsonEntity
{
    public function toJson(bool $isDebugging = false): string;
}
