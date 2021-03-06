<?php

namespace Batmaid\Calendar\Ui;

use Batmaid\Calendar\Exception\CalendarDayException;
use Batmaid\Calendar\Exception\TimeSlotRangeException;
use Batmaid\Calendar\Query\TimeSlotFactory;
use Predis\Client;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Batmaid\Calendar\Calendar;

class GetAvailableAgents extends CalendarUi
{
    protected function configure()
    {
        $this->setDescription('Get Available Agents identifiers');

        $this->addArgument('day', InputArgument::REQUIRED, 'Date in (YYYY-MM-DD) format');
        $this->addArgument('location', InputArgument::REQUIRED);
    }

    /**
     * @throws CalendarDayException
     * @throws TimeSlotRangeException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->initiateSymfonyStyle($input, $output);

        try {
            $dateTime = new \DateTime($input->getArgument('day'));
            $locationId = $input->getArgument('location');

            $calendar = new Calendar(new Client('tcp://localhost'));
            $day = $calendar->getDay($dateTime, $locationId);

            $this->io->section('Search for available Agents on ' . $dateTime->format('l jS \of F Y'));

            $availableAgentsIds = $calendar->getAvailableAgentsIds(
                $day,
                TimeSlotFactory::createFromTimeSlotIndex($this->io->ask('From', '0900')),
                TimeSlotFactory::createFromTimeSlotIndex($this->io->ask('To', '1200'))
            );

            if ($availableAgentsIds) {
                $this->io->success('Number of Agents available: ' . count($availableAgentsIds));
            } else {
                $this->io->warning('No available Agents found');
            }

            $this->printMemoryUsageNotice();
        } catch (\Exception $e) {
            $this->io->error($e->getMessage());
        }
    }
}