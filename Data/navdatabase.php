<?php

namespace SteveEngine\Data;

use Moduls\Bridges\ICompanyBridge;

class NavDatabase extends Database{
    private $tableOfCompanies;
    private $dataOfCompanyId;
    private $tableOfInvoices;
    private $tableOfLines;
    private $whereForCompanies;
    private $companyType;
    private $companies;

    public function init( array $config ){
        $this->prepare( $config["connection"], false );
        $this->tableOfCompanies = $config["tableOfCompanies"];
        $this->dataOfCompanyId = $config["dataOfCompanyId"];
        $this->tableOfInvoices = $config["tableOfInvoices"];
        $this->tableOfLines = $config["tableOfLines"];
        $this->whereForCompanies = $config["whereForCompanies"];
        $this->companyType = $config["companyType"];

        $this->companies = $this->getCompanies();
    }

    public function getCompanies() : array{
        $result = [];
        $query = "
                select *
                from $this->tableOfCompanies
                where $this->whereForCompanies";
        $data = $this->query( $query )->answer( "Moduls\Input\InputCompany" )->select();
        foreach( $data as $row ){
            $result[] = $this->companyType::make($row);
        }
        return $result;
    } 

    public function getInvoices() : array{
        $result = [];
        foreach ( $this->companies as $company ){
            $invoices = $this->getInvoicesByCompanyId( $company->shortName() );
            if (isset($invoices)){
                $result[] = ["companyId" => $company->id(), "invoices" => $invoices];
            }
        }
        return $result;
    }

    public function getCompanyById( int $_id ){
        foreach ( $this->companies as $company ){
            if ( $_id == $company->id() ){
                return $company;
            }
        }
        return null;
    }

    private function getInvoicesByCompanyId( string $_databaseName) {
        //Számlák betöltése
        $invoices = null;
        $query = "
                select inv.*, t1.countrycode as Sz_országkód, t2.countrycode as V_országkód  
                from $_databaseName.$this->tableOfInvoices as inv
                left join companydata.countries as t1 on sz_ország=t1.countryname
                left join companydata.countries as t2 on v_ország=t2.countryname
                where számlakelte>='2021-03-31' and onlinestatus<1 limit 20";
        
        $invoices = $this->query( $query )->answer( "Moduls\Input\InputInvoice" )->select();

        //Tételek betöltése
        if (isset( $invoices )){
            foreach( $invoices as $index => $invoice ){
                $lines = [];
                $query = "
                    select order_id, típus, kód, tételszöveg1, tételszöveg2, mennyiség, megység, nettó, áfa
                    from $_databaseName.$this->tableOfLines
                    where év=".$invoice->Év." and számla_id=".$invoice->Számlaszám." order by order_id";
                
                $lines = $this->query( $query )->select();
                $invoice->lines = $lines;
            }
        }
        return $invoices;
    }

    public function saveTransactionId( ICompanyBridge $company, string $transactionId, array $invoices){
        foreach ($invoices as $index => $invoice){
            $transactionIndex = $index + 1;
            $tableName = $company->shortName();
            $query = "
                update $tableName.számlák
                set onlinestatus=1, transactioncode=:transactionCode, transactionindex=:transactionIndex
                where megjelenítettszámlaszám=:invoiceNumber;";
            $params = [
                "transactionCode" => $transactionId,
                "transactionIndex" => $transactionIndex,
                "invoiceNumber" => $invoice->invoiceNumber()
            ];
            $this->query( $query )->params( $params )->run();
            console($params);
        } 
    }

    public function getTransactionCodes():array{
        $result = [];
        foreach ($this->companies as $company){
            $transactionCodes = $this->getTransactionCodesByCompanyId($company->shortName());
            if (isset($transactionCodes)){
                $result[] = ["companyId" => $company->id(), "transactionCodes" => $transactionCodes];
            }
        }
        return $result;
    }

    private function getTransactionCodesByCompanyId(string $_databaseName){
        //A tranzakciós kódok betöltése betöltése
        $query = "
                select distinct(transactionCode) as transactionCode
                from $_databaseName.számlák
                where számlakelte>='2020-06-29' and onlinestatus=1";
        return db()->query( $query )->answer( "stdClass" )->select(); 
    }

    public function saveTransactionStatus( ICompanyBridge $company, string $transactionCode, int $index, int $status){
        $query = "update " . $company->shortName() . ".számlák
            set onlinestatus=$status
            where transactioncode='$transactionCode' and transactionindex=$index";
        db()->query( $query )->run();
    }
}