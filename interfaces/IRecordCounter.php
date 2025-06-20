<?php
interface IRecordCounter {
    public function countRecords(array $conditions = []): int;
}