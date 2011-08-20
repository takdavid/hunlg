<?php

require_once('grammar.php');

class EmberTest extends PHPUnit_Framework_TestCase
{

    /*
    public function runAll()
    {
        $this->testAll();
    }

    public function assertEquals($expected, $actual)
    {
        assert('$expected === $actual');
        print "$actual\n";
    }
     */

    public function testWordForm()
    {
        $ember = parseNP('ember');

        $actual = $ember->makeNominativus()->vow;
        $expected = $ember->vow;
        $this->assertEquals($expected, $actual);

    }

    public function testPhonology()
    {
        $ember = parseNP('ember');

        $ember->addSuffix('kA');
        $actual = (string) $ember;
        $expected = 'emberke';
        $this->assertEquals($expected, $actual);

        $actual = Phonology::getVowSeq('árvíztűrő tükörfúrógép');
        $expected = '00hhhhllh';
        $this->assertEquals($expected, $actual);

        $actual = Phonology::getVow('ember');
        $expected = 'high';
        $this->assertEquals($expected, $actual);

        $actual = Phonology::getVow('ház');
        $expected = 'opening';
        $this->assertEquals($expected, $actual);

        $actual = Phonology::getVow('út');
        $expected = 'low';
        $this->assertEquals($expected, $actual);

    }

    public function testNominalCases()
    {
        $ember = parseNP('ember');

        $actual = (string) $ember->makeNominativus();
        $expected = 'ember';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ember->makePlural();
        $expected = 'emberek';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ember->makeAccusativus();
        $expected = 'embert';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ember->makePlural()->makeAccusativus();
        $expected = 'embereket';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ember->makeAccusativus()->makePlural();
        $expected = 'embereket';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ember->makeCausalisFinalis();
        $expected = 'emberért';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ember->makePlural()->makeCausalisFinalis();
        $expected = 'emberekért';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ember->makeCausalis()->pronominalize();
        $expected = 'miatta';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ember->makeDativus();
        $expected = 'embernek';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ember->makePlural()->makeDativus();
        $expected = 'embereknek';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ember->makePlural()->makeGenitivus();
        $expected = 'embereknek';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ember->makeInstrumentalis();
        $expected = 'emberrel';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ember->makePlural()->makeInstrumentalis();
        $expected = 'emberekkel';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ember->makeTranslativusFactivus();
        $expected = 'emberré';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ember->makeFormativus();
        $expected = 'emberként';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ember->makeEssivusFormalis();
        $expected = 'emberül';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ember->makeIllativus();
        $expected = 'emberbe';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ember->makeElativus();
        $expected = 'emberből';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ember->makeSublativus();
        $expected = 'emberre';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ember->makeSuperessivus();
        $expected = 'emberen';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ember->makeDelativus();
        $expected = 'emberről';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ember->makeAllativus();
        $expected = 'emberhez';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ember->makeAdessivus();
        $expected = 'embernél';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ember->makeAblativus();
        $expected = 'embertől';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ember->makeTerminativus();
        $expected = 'emberig';
        $this->assertEquals($expected, $actual);

        $ut = parseNP('út');

        $actual = (string) $ut->makeCausalisFinalis();
        $expected = 'útért';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ut->makeInstrumentalis();
        $expected = 'úttal';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ut->makeDativus();
        $expected = 'útnak';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ut->makeFormativus();
        $expected = 'útként';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ut->makeEssivusFormalis();
        $expected = 'útul';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ut->makeIllativus();
        $expected = 'útba';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ut->makeInessivus();
        $expected = 'útban';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ut->makeElativus();
        $expected = 'útból';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ut->makeSublativus();
        $expected = 'útra';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ut->makeDelativus();
        $expected = 'útról';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ut->makeAdessivus();
        $expected = 'útnál';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ut->makeAblativus();
        $expected = 'úttól';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ut->makeTerminativus();
        $expected = 'útig';
        $this->assertEquals($expected, $actual);

    }

    public function testVirtualNominalCases()
    {
        $ember = parseNP('ember');

        $actual = (string) $ember->makeGenitivus();
        $expected = 'embernek';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ember->makeCausalis();
        $expected = 'ember miatt';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ember->makePerlativus();
        $expected = 'emberen keresztül';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ember->makeProlativus();
        $expected = 'emberen át';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ember->makeVialis();
        $expected = 'emberen át';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ember->makeSubessivus();
        $expected = 'ember alatt';
        $this->assertEquals($expected, $actual);

        $actual = (string) $ember->makeProsecutivus();
        $expected = 'ember mentén';
        $this->assertEquals($expected, $actual);

    }

}

//$test = new EmberTest();
//$test->runAll();

?>
