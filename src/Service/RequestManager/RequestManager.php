<?php
/**
 * Created by Marco Barrella <marco@barrella.it>.
 * User: marcobarrella
 * Date: 12/03/2020
 * Time: 22:51
 */

namespace App\Service\RequestManager;

use Symfony\Component\HttpFoundation\RequestStack;

class RequestManager
{
    const PAGE_VAR = 'page';

    const PER_PAGE_VAR = 'per_page';

    const ORDER_VAR = 'order';

    private $request;

    public function __construct(RequestStack $request)
    {
        $this->request = $request->getCurrentRequest();
    }

    /**
     * Get $_POST data
     * @return array
     */
    public function getPayload(): array
    {
        return $this->request->request->all();
    }

    /**
     * Get pagination page
     * @return int
     */
    public function getPage(): int
    {
        return $this->request->get(self::PAGE_VAR, 0);
    }

    /**
     * Get pagination size
     * @return int
     */
    public function getSize(): int
    {
        return $this->request->get(self::PER_PAGE_VAR, 10);
    }

    /**
     * Get search filter from query string
     * @example name=foo&email=example@example.com&order=+name -> [name => foo, email => example@example.com]
     * @return array
     */
    public function getSearch(): array
    {
        $qs = $this->request->query->all();

        // Flush from array variable used for other scopes.

        unset($qs[self::ORDER_VAR]);

        unset($qs[self::PAGE_VAR]);

        unset($qs[self::PER_PAGE_VAR]);

        return $qs;
    }

    /**
     * Get order of result by query string
     * @example order=+creation_date,-username -> [creation_date => DESC, username => ASC]
     * @return array
     */
    public function getOrder(): array
    {
        $order = $this->request->query->get(self::ORDER_VAR, ''); // Get order value from query string

        $order = explode(',', $order); // Get comma separated values of order variable

        $orderArray = [];

        array_walk($order, function (&$item) use(&$orderArray) {

            if(strpos($item, '-') !== false) { // if order is desc

                // Removes initial minus sign and append DESC suffix
                $item = str_replace('-', '', $item);

                $orderArray[$item] = 'DESC';

            } else {

                // Removes initial plus sign (if is provided) and append ASC suffix
                $item = str_replace('+', '', $item);

                $orderArray[$item] = 'ASC';
            }

        });

        return $orderArray;
    }
}