<?php

namespace App\Command;

use App\Service\Command\WishlistExporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class WishlistExporterCommand extends Command
{
    protected static $defaultName = 'wishlist:exporter';

    private $wishListExporter;

    public function __construct(WishlistExporter $wishlistExporter)
    {
        $this->wishListExporter = $wishlistExporter;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Export Wishlists to CSV');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->wishListExporter->export();

        $io->success('File exported under var/output folder');

        return 0;
    }
}
