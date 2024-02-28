<?php


namespace App\Services;

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Label\Margin\Margin;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter as LabelAlignmentCenterAlias;

class QrcodeService2

{
    /**
     * @var BuilderInterface
     */
    protected $builder;

    public function __construct(BuilderInterface $builder)
    {
        $this->builder = $builder;
    }
    public function qrcode($query)
    {
        $url='http://localhost/ProjectPi/public/index.php/listclient';
        $objDateTime= new \DateTime('NOW');
        $dateString= $objDateTime->format('d-m-Y H:i:s');

        $path=dirname(__DIR__,2).'/public/assets/';

        //set qrcode
        $result=$this->builder
            ->data($url.$query)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(200)
            ->margin(10)
            ->labelText($dateString)
            ->labelAlignment(new LabelAlignmentCenterAlias())
            ->labelMargin(new Margin(15, 5, 5, 5))
            ->logoPath((\dirname(__DIR__,2).'/public/back/img/logo.png'))
            ->logoResizeToWidth('100')
            ->logoResizeToHeight('100')
            ->backgroundColor(new Color(255,165,0))
            ->build();

        //genere le name
        $namePng =uniqid('',''). '.png';
//enregistre limg
        $result->saveToFile( (\dirname(__DIR__,2).'/public/uploads/qr-code/'.$namePng));
//je retourne la reponse
        return $result->getDataUri(); //recupere mon image
    }


}