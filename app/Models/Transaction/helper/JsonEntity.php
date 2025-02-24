<?php

interface JsonEntity
{
    public function toJson(bool $isDebugging = false): string;
}
