<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Botble\Filescode\Models\Filescode;
use Botble\Filescode\Models\Code;
use Log;
use DB;
use Box\Spout\Common\Type;

class importAppleCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'd2t:importAppleCode {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $file = $this->argument('file');
        $objFile = Filescode::find($file);
        $numInserted = 100;
        
        if ($objFile) {
            $filePath = $objFile->file;
            $file = public_path('storage/') .  $filePath;

            \Log::debug($file);

            if (file_exists($file)) {
                //Viet logic xu ly import o day

                $objFile->status = 'Processing';
                $objFile->save();

                //Delete all data code by file id
                Code::where('file', $objFile->id)->delete();

                try {
                    //code...
                    $reader = \Box\Spout\Reader\ReaderFactory::create(Type::XLSX);
                    $reader->setShouldFormatDates(true);
                    $reader->open($file);

                    $id = 1;
                    $idHasData = 9;

                    foreach ($reader->getSheetIterator() as $sheet) {
                        $aryDataInsert = [];
                        foreach ($sheet->getRowIterator() as $row) {
                            if ($id < $idHasData) {
                                $id++;
                                continue;
                            }

                            $id++;

                            $item = [
                                'code' => $row[0],
                                'url' => $row[2],
                                'file' => $objFile->id,
                                'status' => 'none',
                                'viewed_at' => null,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s')
                            ];

                            $aryDataInsert[] = $item;

                            if (count($aryDataInsert) == $numInserted) {
                                //Xu ly insert du lieu
                                Code::insert($aryDataInsert);
                                $aryDataInsert = [];
                                $this->info('Inserted data 100 row');
                                \Log::debug('Inserted data 100 row');
                            }

                            $this->info('Process code: '. $row[0]);
                            \Log::debug('Process code: '. $row[0]);
                        }
                    }

                    if (count($aryDataInsert)) {
                        //Xu ly insert du lieu
                        Code::insert($aryDataInsert);
                        $aryDataInsert = [];
                        $this->info('Inserted data '. count($aryDataInsert) .' row');
                        \Log::debug('Inserted data '. count($aryDataInsert) .' row');
                    }


                    $objFile->status = 'Processed';
                    $objFile->save();

                    DB::commit();
                } catch (\Throwable $th) {
                    \Log::error($th->getMessage());
                    $objFile->status = 'Process Error';
                    $objFile->save();
                    DB::rollBack();
                }
                
            } else {
                //Thong bao loi o day
                Log::error("File not found");
            }
        }else{
            $this->error("File not found");
        }
    }
}