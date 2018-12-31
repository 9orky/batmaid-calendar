<?php

namespace Batmaid\Calendar\Ui;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CalendarUi extends Command
{
    /**
     * @var SymfonyStyle
     */
    protected $io;

    protected function initiateSymfonyStyle(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->io->title('CALENDAR');
    }

    protected function getYearValidator(): callable
    {
        return function($value) {
            if ($value < 1970 || $value > 9999) {
                throw new \RuntimeException('Range for year is 1970 - 9999');
            }

            return $value;
        };
    }

    protected function getDayOfYearValidator(int $year): callable
    {
        return function($value) use ($year) {
            if (1 > $value || $value > 366) {
                throw new \RuntimeException('Wrong day number');
            }

            return $value;
        };
    }

    protected function getAgentIdValidator(): callable
    {
        return function(int $value) {
            if (0 >= $value) {
                throw new \RuntimeException('Agent ID must be positive integer');
            }

            return $value;
        };
    }

    protected function printMemoryUsageNotice(): void
    {
        $memoryUsage = memory_get_usage(true) / 1024 / 1024;
        $this->io->note("Memory used: ${memoryUsage}M");
    }
}