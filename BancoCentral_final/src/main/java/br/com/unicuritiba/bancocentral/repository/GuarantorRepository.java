package br.com.unicuritiba.bancocentral.repository;

import br.com.unicuritiba.bancocentral.model.Guarantor;
import org.springframework.data.jpa.repository.JpaRepository;

public interface GuarantorRepository extends JpaRepository<Guarantor, Long> {
}
