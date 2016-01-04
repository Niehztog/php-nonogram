<?php

namespace Nonogram\Label\Provider;

interface AnyLabelProvider
{
    /**
     * @return array
     */
    public function getLabelsForColumn();

    /**
     * @return array
     */
    public function getLabelsForRow();

}
