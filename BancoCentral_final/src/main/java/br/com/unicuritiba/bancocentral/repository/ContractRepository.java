package br.com.unicuritiba.bancocentral.repository;

import org.springframework.data.jpa.repository.JpaRepository;
import br.com.unicuritiba.bancocentral.model.Contract;


public interface ContractRepository extends JpaRepository<Contract, Long> {
}
