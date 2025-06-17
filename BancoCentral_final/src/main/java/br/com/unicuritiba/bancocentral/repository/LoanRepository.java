
package br.com.unicuritiba.bancocentral.repository;

import br.com.unicuritiba.bancocentral.model.Loan;
import org.springframework.data.jpa.repository.JpaRepository;

public interface LoanRepository extends JpaRepository<Loan, Long> {
}
