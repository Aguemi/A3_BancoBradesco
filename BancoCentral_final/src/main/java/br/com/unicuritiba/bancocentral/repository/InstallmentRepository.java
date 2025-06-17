package br.com.unicuritiba.bancocentral.repository;

import br.com.unicuritiba.bancocentral.model.Installment;
import org.springframework.data.jpa.repository.JpaRepository;

public interface InstallmentRepository extends JpaRepository<Installment, Long> {
}
