<?php

namespace Kinglet\Container;

interface ContainerAwareInterface {

    /**
     * @param ContainerInterface $container
     */
    public function setContainer( ContainerInterface $container );

}
