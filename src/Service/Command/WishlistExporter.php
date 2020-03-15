<?php
/**
 * Created by Marco Barrella <marco@barrella.it>.
 * User: marcobarrella
 * Date: 15/03/2020
 * Time: 23:20
 */

namespace App\Service\Command;


use App\Repository\UserRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class WishlistExporter
{
    private $userRepository;

    private $parameterBag;

    public function __construct(UserRepository $userRepository, ParameterBagInterface $parameterBag)
    {
        $this->userRepository = $userRepository;

        $this->parameterBag = $parameterBag;
    }

    public function export()
    {
        $users = $this->userRepository->findAll();

        $varFolder = $this->parameterBag->get('kernel.project_dir') . '/var/app_export';

        if(! file_exists($varFolder)) {

            mkdir($varFolder, 0777);
        }

        $fp = fopen($varFolder.'/wishlist_exporter_'.date('YmdHis').'.csv', 'w');

        fputcsv($fp, ['user', 'title wishlist', 'number of items']);

        foreach ($users as $user) {

            foreach ($user->getWishlists() as $wishlist) {

                fputcsv($fp, [$user->getEmail(), $wishlist->getName(), $wishlist->getWishlistItems()->count()]);
            }
        }

        fclose($fp);
    }
}