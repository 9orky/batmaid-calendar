<?php

namespace Batmaid\Calendar\Ui;

use Batmaid\Calendar\Query\TimeSlotFactory;
use Predis\Client;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Batmaid\Calendar\Calendar;

class RemoveAvailability extends CalendarUi
{
    protected function configure()
    {
        $this->setDescription('Removes Agent available Time Slots from the Calendar');

        $this->addArgument('day', InputArgument::REQUIRED, 'Date in (YYYY-MM-DD) format');
        $this->addArgument('location', InputArgument::REQUIRED);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->initiateSymfonyStyle($input, $output);

        try {
            $dateTime = new \DateTime($input->getArgument('day'));
            $locationId = $input->getArgument('location');

            $calendar = new Calendar(new Client('tcp://localhost'));
            $day = $calendar->getDay($dateTime, $locationId);

            $this->io->section('Remove Time Slots for the Agent on ' . $dateTime->format('l jS \of F Y'));

            $agentId = $this->io->ask('Agent ID');
            $from = TimeSlotFactory::createFromTimeSlotIndex($this->io->ask('From', '0900'));
            $to = TimeSlotFactory::createFromTimeSlotIndex($this->io->ask('To', '1200'));

            $calendar->removeHours($day, $from, $to, $agentId);

            $this->io->success('Availability removed from the Calendar');
            $this->printMemoryUsageNotice();
        } catch (\Exception $e) {
            $this->io->error($e->getMessage());
        }
    }
}