<?php

namespace GedBundle\Twig\Extension;

class sizeExtension extends \Twig_Extension
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'size';
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('humanSize', array( $this, 'humanSizeFilter' )),
        );
    }

    public function humanSizeFilter($size)
    {
        $units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
        $power = $size > 0 ? floor(log($size, 1024)) : 0;
        return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
    }
}
