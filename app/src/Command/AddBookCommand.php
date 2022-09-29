<?php

namespace App\Command;

use App\Factory\BookFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'add-book',
    description: 'create a book in the database',
)]
class AddBookCommand extends Command
{
    public function __construct(private EntityManagerInterface $manager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('title', InputArgument::REQUIRED, 'title of the book')
            ->addArgument('coverText', InputArgument::REQUIRED, 'back cover content of the book')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $title = $input->getArgument('title');


        if ($title) {
            $io->note(sprintf('Title as: %s', $title));
        }

        $coverText = $input->getArgument('coverText');

        if ($coverText) {
            $io->note(sprintf('Cover text as: %s', $coverText));
        }

        $entity = BookFactory::createBook($title, $coverText);
        $this->manager->persist($entity);
        $this->manager->flush();

//        if ($input->getOption('option1')) {
//            // ...
//        }

        $io->success('Book is saved');

        return Command::SUCCESS;
    }
}
